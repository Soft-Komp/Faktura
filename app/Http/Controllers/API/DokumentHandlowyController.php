<?php

namespace App\Http\Controllers\API;

use App\Models\DokumentHandlowy;
use App\Models\PozycjaDokumentu;
use App\Models\Firma;
use App\Models\Odbiorca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PDF;

class DokumentHandlowyController extends BaseController {

    /**
     * Wyświetl listę dokumentów handlowych
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        $query = DokumentHandlowy::with(['firma', 'odbiorca']);

        // Filtrowanie po typie dokumentu
        if ($request->has('typ_dokumentu')) {
            $query->where('typ_dokumentu', $request->typ_dokumentu);
        }

        // Filtrowanie po dacie wystawienia
        if ($request->has('data_od') && $request->has('data_do')) {
            $query->whereBetween('data_wystawienia', [$request->data_od, $request->data_do]);
        }

        // Filtrowanie po odbiorcy
        if ($request->has('id_odbiorca')) {
            $query->where('id_odbiorca', $request->id_odbiorca);
        }

        // Filtrowanie po statusie
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $dokumenty = $query->orderBy('data_wystawienia', 'desc')->get();

        return $this->sendResponse($dokumenty, 'Dokumenty handlowe pobrane pomyślnie');
    }

    /**
     * Zapisz nowy dokument handlowy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        // Sprawdzenie uprawnień
        $user = auth()->user();
        $idFirmy = $request->input('id_firma');

        // Jeśli nie podano id_firma, ustaw firmę użytkownika
        if (!$idFirmy) {
            $idFirmy = $user->firma_id;
            $request->merge(['id_firma' => $idFirmy]);
        }

        // Jeśli użytkownik próbuje wystawić fakturę jako inna firma
        if ($idFirmy != $user->firma_id) {
            // Sprawdź czy użytkownik jest z firmy księgowej i czy ta firma obsługuje klienta
            $firma = Firma::findOrFail($idFirmy);

            if (!$user->firma->jestFirmaKsiegowa() || $firma->firma_ksiegowa_id != $user->firma_id) {
                return $this->sendError('Brak uprawnień do wystawiania faktur w imieniu tej firmy.', [], 403);
            }
        }

        $validator = Validator::make($request->all(), [
                    'typ_dokumentu' => 'required|in:FV,FK,FMP,FMK,PA,WZ,PZ',
                    'data_sprzedazy' => 'required|date',
                    'data_wystawienia' => 'required|date',
                    'termin_platnosci' => 'required|date',
                    'miejsce_wystawienia' => 'required|max:100',
                    'id_firma' => 'required|exists:firmy,id',
                    'id_odbiorca' => 'required|exists:odbiorcy,id',
                    'waluta' => 'required|max:3',
                    'kurs_waluty' => 'required_if:waluta,!=,PLN|numeric',
                    'uwagi' => 'nullable|max:255',
                    'pozycje' => 'required|array|min:1',
                    'pozycje.*.id_artykulu' => 'required|exists:artykuly,id',
                    'pozycje.*.nazwa_pozycji' => 'required|max:100',
                    'pozycje.*.ilosc' => 'required|numeric|min:0.001',
                    'pozycje.*.jednostka' => 'required|max:10',
                    'pozycje.*.cena_netto' => 'required|numeric|min:0',
                    'pozycje.*.stawka_vat' => 'required|numeric|between:0,100',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors(), 422);
        }

        // Pobierz firmę, aby uzyskać prefix faktury
        $firma = Firma::findOrFail($request->id_firma);

        // Generuj numer dokumentu
        $ostatniDokument = DokumentHandlowy::where('typ_dokumentu', $request->typ_dokumentu)
                ->whereYear('data_wystawienia', date('Y'))
                ->orderBy('id', 'desc')
                ->first();

        $numerPorzadkowy = 1;
        if ($ostatniDokument) {
            // Wyciągnij numer porządkowy z ostatniego dokumentu
            $czesci = explode('/', $ostatniDokument->numer);
            if (count($czesci) > 1) {
                $numerPorzadkowy = (int) $czesci[0] + 1;
            }
        }

        $numer = sprintf("%d/%s/%s/%s",
                $numerPorzadkowy,
                $request->typ_dokumentu,
                $firma->prefix_faktury,
                date('Y', strtotime($request->data_wystawienia))
        );

        try {
            DB::beginTransaction();

            // Oblicz wartości dokumentu
            $wartoscNetto = 0;
            $wartoscVat = 0;
            $wartoscBrutto = 0;

            foreach ($request->pozycje as $pozycja) {
                $wartoscNettoPozycji = $pozycja['ilosc'] * $pozycja['cena_netto'];
                $wartoscVatPozycji = $wartoscNettoPozycji * ($pozycja['stawka_vat'] / 100);
                $wartoscBruttoPozycji = $wartoscNettoPozycji + $wartoscVatPozycji;

                $wartoscNetto += $wartoscNettoPozycji;
                $wartoscVat += $wartoscVatPozycji;
                $wartoscBrutto += $wartoscBruttoPozycji;
            }

            // Utwórz dokument
            $dokument = DokumentHandlowy::create([
                        'numer' => $numer,
                        'typ_dokumentu' => $request->typ_dokumentu,
                        'data_sprzedazy' => $request->data_sprzedazy,
                        'data_wystawienia' => $request->data_wystawienia,
                        'termin_platnosci' => $request->termin_platnosci,
                        'miejsce_wystawienia' => $request->miejsce_wystawienia,
                        'id_firma' => $request->id_firma,
                        'id_odbiorca' => $request->id_odbiorca,
                        'waluta' => $request->waluta,
                        'kurs_waluty' => $request->waluta === 'PLN' ? 1 : $request->kurs_waluty,
                        'uwagi' => $request->uwagi,
                        'wartosc_netto' => $wartoscNetto,
                        'wartosc_vat' => $wartoscVat,
                        'wartosc_brutto' => $wartoscBrutto,
                        'status' => 'wystawiony'
            ]);

            // Dodaj pozycje dokumentu
            foreach ($request->pozycje as $pozycja) {
                $wartoscNettoPozycji = $pozycja['ilosc'] * $pozycja['cena_netto'];
                $wartoscVatPozycji = $wartoscNettoPozycji * ($pozycja['stawka_vat'] / 100);
                $wartoscBruttoPozycji = $wartoscNettoPozycji + $wartoscVatPozycji;
                $cenaBrutto = $pozycja['cena_netto'] * (1 + ($pozycja['stawka_vat'] / 100));

                PozycjaDokumentu::create([
                    'id_dokumentu' => $dokument->id,
                    'id_artykulu' => $pozycja['id_artykulu'],
                    'nazwa_pozycji' => $pozycja['nazwa_pozycji'],
                    'ilosc' => $pozycja['ilosc'],
                    'jednostka' => $pozycja['jednostka'],
                    'cena_netto' => $pozycja['cena_netto'],
                    'stawka_vat' => $pozycja['stawka_vat'],
                    'wartosc_vat' => $wartoscVatPozycji,
                    'cena_brutto' => $cenaBrutto,
                    'wartosc_netto_pozycji' => $wartoscNettoPozycji,
                    'wartosc_brutto_pozycji' => $wartoscBruttoPozycji,
                    'uwagi' => $pozycja['uwagi'] ?? null
                ]);
            }

            DB::commit();

            // Pobierz dokument z relacjami
            $dokument = DokumentHandlowy::with(['firma', 'odbiorca', 'pozycje.artykul'])->find($dokument->id);

            return $this->sendResponse($dokument, 'Dokument handlowy utworzony pomyślnie');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Błąd podczas tworzenia dokumentu', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Wyświetl szczegóły dokumentu handlowego
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        $dokument = DokumentHandlowy::with(['firma', 'odbiorca', 'pozycje.artykul'])->find($id);

        if (is_null($dokument)) {
            return $this->sendError('Dokument handlowy nie istnieje');
        }

        return $this->sendResponse($dokument, 'Dokument handlowy pobrany pomyślnie');
    }

    /**
     * Aktualizuj dokument handlowy
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {

        // Sprawdzenie uprawnień
        $user = auth()->user();
        $idFirmy = $request->input('id_firma');

        // Jeśli użytkownik próbuje wystawić fakturę jako inna firma
        if ($idFirmy != $user->firma_id) {
            // Sprawdź czy użytkownik jest z firmy księgowej i czy ta firma obsługuje klienta
            $firma = Firma::findOrFail($idFirmy);

            if (!$user->firma->jestFirmaKsiegowa() || $firma->firma_ksiegowa_id != $user->firma_id) {
                return $this->sendError('Brak uprawnień do wystawiania faktur w imieniu tej firmy.', [], 403);
            }
        }

        $dokument = DokumentHandlowy::find($id);

        if (is_null($dokument)) {
            return $this->sendError('Dokument handlowy nie istnieje');
        }

        // Sprawdź czy dokument można edytować
        if ($dokument->status === 'zatwierdzony') {
            return $this->sendError('Nie można edytować zatwierdzonego dokumentu');
        }

        $validator = Validator::make($request->all(), [
                    'data_sprzedazy' => 'required|date',
                    'data_wystawienia' => 'required|date',
                    'termin_platnosci' => 'required|date',
                    'miejsce_wystawienia' => 'required|max:100',
                    'id_odbiorca' => 'required|exists:odbiorcy,id',
                    'waluta' => 'required|max:3',
                    'kurs_waluty' => 'required_if:waluta,!=,PLN|numeric',
                    'uwagi' => 'nullable|max:255',
                    'pozycje' => 'required|array|min:1',
                    'pozycje.*.id' => 'nullable|exists:pozycje_dokumentu,id',
                    'pozycje.*.id_artykulu' => 'required|exists:artykuly,id',
                    'pozycje.*.nazwa_pozycji' => 'required|max:100',
                    'pozycje.*.ilosc' => 'required|numeric|min:0.001',
                    'pozycje.*.jednostka' => 'required|max:10',
                    'pozycje.*.cena_netto' => 'required|numeric|min:0',
                    'pozycje.*.stawka_vat' => 'required|numeric|between:0,100',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors());
        }

        try {
            DB::beginTransaction();

            // Oblicz wartości dokumentu
            $wartoscNetto = 0;
            $wartoscVat = 0;
            $wartoscBrutto = 0;

            // Usuń istniejące pozycje, które nie są w nowym zestawie
            $istniejaceIds = collect($request->pozycje)
                    ->pluck('id')
                    ->filter()
                    ->toArray();

            PozycjaDokumentu::where('id_dokumentu', $dokument->id)
                    ->whereNotIn('id', $istniejaceIds)
                    ->delete();

            // Aktualizuj lub dodaj pozycje
            foreach ($request->pozycje as $pozycja) {
                $wartoscNettoPozycji = $pozycja['ilosc'] * $pozycja['cena_netto'];
                $wartoscVatPozycji = $wartoscNettoPozycji * ($pozycja['stawka_vat'] / 100);
                $wartoscBruttoPozycji = $wartoscNettoPozycji + $wartoscVatPozycji;
                $cenaBrutto = $pozycja['cena_netto'] * (1 + ($pozycja['stawka_vat'] / 100));

                $wartoscNetto += $wartoscNettoPozycji;
                $wartoscVat += $wartoscVatPozycji;
                $wartoscBrutto += $wartoscBruttoPozycji;

                $pozycjaDane = [
                    'id_dokumentu' => $dokument->id,
                    'id_artykulu' => $pozycja['id_artykulu'],
                    'nazwa_pozycji' => $pozycja['nazwa_pozycji'],
                    'ilosc' => $pozycja['ilosc'],
                    'jednostka' => $pozycja['jednostka'],
                    'cena_netto' => $pozycja['cena_netto'],
                    'stawka_vat' => $pozycja['stawka_vat'],
                    'wartosc_vat' => $wartoscVatPozycji,
                    'cena_brutto' => $cenaBrutto,
                    'wartosc_netto_pozycji' => $wartoscNettoPozycji,
                    'wartosc_brutto_pozycji' => $wartoscBruttoPozycji,
                    'uwagi' => $pozycja['uwagi'] ?? null
                ];

                if (isset($pozycja['id'])) {
                    // Aktualizuj istniejącą pozycję
                    PozycjaDokumentu::where('id', $pozycja['id'])->update($pozycjaDane);
                } else {
                    // Dodaj nową pozycję
                    PozycjaDokumentu::create($pozycjaDane);
                }
            }

            // Aktualizuj dokument
            $dokument->update([
                'data_sprzedazy' => $request->data_sprzedazy,
                'data_wystawienia' => $request->data_wystawienia,
                'termin_platnosci' => $request->termin_platnosci,
                'miejsce_wystawienia' => $request->miejsce_wystawienia,
                'id_odbiorca' => $request->id_odbiorca,
                'waluta' => $request->waluta,
                'kurs_waluty' => $request->waluta === 'PLN' ? 1 : $request->kurs_waluty,
                'uwagi' => $request->uwagi,
                'wartosc_netto' => $wartoscNetto,
                'wartosc_vat' => $wartoscVat,
                'wartosc_brutto' => $wartoscBrutto,
            ]);

            DB::commit();

            // Pobierz zaktualizowany dokument z relacjami
            $dokument = DokumentHandlowy::with(['firma', 'odbiorca', 'pozycje.artykul'])->find($dokument->id);

            return $this->sendResponse($dokument, 'Dokument handlowy zaktualizowany pomyślnie');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Błąd podczas aktualizacji dokumentu', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Usuń dokument handlowy
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        $dokument = DokumentHandlowy::find($id);

        if (is_null($dokument)) {
            return $this->sendError('Dokument handlowy nie istnieje');
        }

        // Sprawdź czy dokument można usunąć
        if ($dokument->status === 'zatwierdzony') {
            return $this->sendError('Nie można usunąć zatwierdzonego dokumentu');
        }

        try {
            DB::beginTransaction();

            // Usuń pozycje dokumentu
            PozycjaDokumentu::where('id_dokumentu', $id)->delete();

            // Usuń dokument
            $dokument->delete();

            DB::commit();

            return $this->sendResponse([], 'Dokument handlowy usunięty pomyślnie');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Błąd podczas usuwania dokumentu', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Generuj PDF dokumentu
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generatePdf($id) {
        $dokument = DokumentHandlowy::with(['firma', 'odbiorca', 'pozycje.artykul'])->find($id);

        if (is_null($dokument)) {
            return $this->sendError('Dokument handlowy nie istnieje');
        }

        // Generowanie PDF z użyciem biblioteki barryvdh/laravel-dompdf
        $pdf = PDF::loadView('pdf.dokument', compact('dokument'));
        $filename = 'dokument_' . str_replace('/', '_', $dokument->numer) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Wyślij dokument emailem
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmail(Request $request, $id) {
        $dokument = DokumentHandlowy::with(['firma', 'odbiorca'])->find($id);

        if (is_null($dokument)) {
            return $this->sendError('Dokument handlowy nie istnieje');
        }

        $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'temat' => 'required|string|max:100',
                    'tresc' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors());
        }

        // Tutaj należy zaimplementować wysyłanie emaila z załączonym PDF
        // Przykładowa implementacja z użyciem Laravel Mail
        // Przykład:
        // Mail::send('emails.dokument', ['tresc' => $request->tresc], function ($message) use ($request, $dokument, $pdf) {
        //     $message->to($request->email)
        //         ->subject($request->temat)
        //         ->attachData($pdf->output(), 'dokument_' . $dokument->numer . '.pdf');
        // });
        // Tymczasowo zwracamy informację o braku implementacji
        return $this->sendResponse(['id' => $id, 'email' => $request->email], 'Funkcja wysyłania emaila zostanie zaimplementowana w przyszłości');
    }

}
