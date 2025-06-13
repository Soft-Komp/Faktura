<?php

namespace App\Http\Controllers\API;

use App\Models\Artykul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArtykulController extends BaseController
{
    /**
     * Wyświetl listę artykułów dostępnych dla użytkownika
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = auth()->user();
        
        // Admini widzą wszystkie artykuły
        if ($user->jestAdmin()) {
            $artykuly = Artykul::with('firma')->orderBy('nazwa')->get();
        } 
        // Księgowi widzą artykuły swojej firmy, firm klientów i publiczne
        elseif ($user->jestKsiegowy() && $user->firma && $user->firma->jestFirmaKsiegowa()) {
            // Pobierz ID firmy księgowej i wszystkich jej klientów
            $firmaIds = [$user->firma_id];
            $klienciIds = $user->firma->klienci->pluck('id')->toArray();
            $firmaIds = array_merge($firmaIds, $klienciIds);
            
            $artykuly = Artykul::with('firma')
                ->where(function($query) use ($firmaIds) {
                    $query->whereIn('firma_id', $firmaIds)
                          ->orWhere('publiczny', true)
                          ->orWhereNull('firma_id'); // Legacy artykuły bez przypisania
                })
                ->orderBy('nazwa')
                ->get();
        }
        // Klienci widzą tylko swoje artykuły i publiczne
        else {
            $artykuly = Artykul::with('firma')
                ->where(function($query) use ($user) {
                    $query->where('firma_id', $user->firma_id)
                          ->orWhere('publiczny', true)
                          ->orWhereNull('firma_id'); // Legacy artykuły bez przypisania
                })
                ->orderBy('nazwa')
                ->get();
        }
        
        return $this->sendResponse($artykuly, 'Artykuły pobrane pomyślnie');
    }

    /**
     * Zapisz nowy artykuł
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nazwa' => 'required|string|max:255',
            'kod_artykulu' => 'required|string|max:50|unique:artykuly',
            'jednostka' => 'required|string|max:20',
            'stawka_vat' => 'required|numeric|between:0,100',
            'cena_netto' => 'required|numeric|min:0',
            'cena_brutto' => 'required|numeric|min:0',
            'opis' => 'nullable|string|max:1000',
            'aktywny' => 'boolean',
            'publiczny' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors(), 422);
        }

        $user = auth()->user();
        $data = $request->all();
        
        // Automatycznie przypisz artykuł do firmy użytkownika
        $data['firma_id'] = $user->firma_id;
        
        // Tylko admin może tworzyć artykuły publiczne
        if (!$user->jestAdmin()) {
            $data['publiczny'] = false;
        }

        $artykul = Artykul::create($data);
        $artykul->load('firma');
        
        return $this->sendResponse($artykul, 'Artykuł utworzony pomyślnie');
    }

    /**
     * Wyświetl szczegóły artykułu
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $artykul = Artykul::with('firma')->find($id);

        if (is_null($artykul)) {
            return $this->sendError('Artykuł nie istnieje');
        }

        $user = auth()->user();
        
        // Sprawdź czy użytkownik ma dostęp do artykułu
        if (!$user->jestAdmin() && !$artykul->dostepnyDlaFirmy($user->firma_id)) {
            return $this->sendError('Brak dostępu do tego artykułu', [], 403);
        }

        return $this->sendResponse($artykul, 'Artykuł pobrany pomyślnie');
    }

    /**
     * Aktualizuj artykuł
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $artykul = Artykul::with('firma')->find($id);

        if (is_null($artykul)) {
            return $this->sendError('Artykuł nie istnieje');
        }

        $user = auth()->user();
        
        // Sprawdź uprawnienia do edycji
        if (!$artykul->mozeBycEdytowanyPrzez($user)) {
            return $this->sendError('Brak uprawnień do edycji tego artykułu', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'nazwa' => 'required|string|max:255',
            'kod_artykulu' => 'required|string|max:50|unique:artykuly,kod_artykulu,' . $id,
            'jednostka' => 'required|string|max:20',
            'stawka_vat' => 'required|numeric|between:0,100',
            'cena_netto' => 'required|numeric|min:0',
            'cena_brutto' => 'required|numeric|min:0',
            'opis' => 'nullable|string|max:1000',
            'aktywny' => 'boolean',
            'publiczny' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors(), 422);
        }

        $data = $request->all();
        
        // Tylko admin może zmieniać status publiczny
        if (!$user->jestAdmin()) {
            unset($data['publiczny']);
        }
        
        // Nie pozwól na zmianę właściciela artykułu (chyba że admin)
        if (!$user->jestAdmin()) {
            unset($data['firma_id']);
        }

        $artykul->update($data);
        $artykul->load('firma');
        
        return $this->sendResponse($artykul, 'Artykuł zaktualizowany pomyślnie');
    }

    /**
     * Usuń artykuł
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $artykul = Artykul::find($id);

        if (is_null($artykul)) {
            return $this->sendError('Artykuł nie istnieje');
        }

        $user = auth()->user();
        
        // Sprawdź uprawnienia do usunięcia
        if (!$artykul->mozeBycEdytowanyPrzez($user)) {
            return $this->sendError('Brak uprawnień do usunięcia tego artykułu', [], 403);
        }

        // Sprawdź czy artykuł nie jest używany w dokumentach
        if ($artykul->pozycjeDokumentu()->count() > 0) {
            return $this->sendError('Nie można usunąć artykułu używanego w dokumentach. Ustaw go jako nieaktywny.', [], 422);
        }

        $artykul->delete();
        
        return $this->sendResponse([], 'Artykuł usunięty pomyślnie');
    }

    /**
     * Zmień status publiczny artykułu (tylko admin)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function togglePublic(Request $request, $id)
    {
        $user = auth()->user();
        
        if (!$user->jestAdmin()) {
            return $this->sendError('Tylko administrator może zmieniać status publiczny artykułów', [], 403);
        }

        $artykul = Artykul::find($id);

        if (is_null($artykul)) {
            return $this->sendError('Artykuł nie istnieje');
        }

        $validator = Validator::make($request->all(), [
            'publiczny' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors(), 422);
        }

        $artykul->publiczny = $request->publiczny;
        $artykul->save();
        $artykul->load('firma');

        return $this->sendResponse($artykul, 'Status publiczny artykułu został zmieniony');
    }

    /**
     * Pobierz artykuły publiczne
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPubliczne()
    {
        $artykuly = Artykul::with('firma')
            ->publiczne()
            ->aktywne()
            ->orderBy('nazwa')
            ->get();
            
        return $this->sendResponse($artykuly, 'Artykuły publiczne pobrane pomyślnie');
    }

    /**
     * Duplikuj artykuł do własnej firmy
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function duplicate($id)
    {
        $artykul = Artykul::find($id);

        if (is_null($artykul)) {
            return $this->sendError('Artykuł nie istnieje');
        }

        $user = auth()->user();
        
        // Sprawdź czy użytkownik ma dostęp do artykułu
        if (!$user->jestAdmin() && !$artykul->dostepnyDlaFirmy($user->firma_id)) {
            return $this->sendError('Brak dostępu do tego artykułu', [], 403);
        }

        // Sprawdź czy artykuł już nie należy do firmy użytkownika
        if ($artykul->firma_id == $user->firma_id) {
            return $this->sendError('Artykuł już należy do Twojej firmy', [], 422);
        }

        // Utwórz kopię artykułu
        $nowyArtykul = $artykul->replicate();
        $nowyArtykul->firma_id = $user->firma_id;
        $nowyArtykul->publiczny = false;
        $nowyArtykul->kod_artykulu = $artykul->kod_artykulu . '_KOPIA_' . time();
        $nowyArtykul->nazwa = $artykul->nazwa . ' (kopia)';
        $nowyArtykul->save();
        $nowyArtykul->load('firma');

        return $this->sendResponse($nowyArtykul, 'Artykuł zduplikowany pomyślnie');
    }
}