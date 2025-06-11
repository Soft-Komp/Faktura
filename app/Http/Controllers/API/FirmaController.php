<?php

namespace App\Http\Controllers\API;

use App\Models\Firma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FirmaController extends BaseController
{
    // Metoda do pobierania klientów firmy księgowej
public function klienci()
{
    $user = auth()->user();
    
    if (!$user->firma || !$user->firma->jestFirmaKsiegowa()) {
        return $this->sendError('Tylko firmy księgowe mogą przeglądać listę klientów.', [], 403);
    }
    
    $klienci = $user->firma->klienci;
    
    return $this->sendResponse($klienci, 'Lista klientów pobrana pomyślnie.');
}

// Metoda do dodawania nowego klienta
public function dodajKlienta(Request $request)
{
    $user = auth()->user();
    
    if (!$user->firma || !$user->firma->jestFirmaKsiegowa()) {
        return $this->sendError('Tylko firmy księgowe mogą dodawać klientów.', [], 403);
    }
    
    $validator = Validator::make($request->all(), [
        'nazwa' => 'required|string|max:100',
        'nazwa_pelna' => 'required|string|max:255',
        'kod_pocztowy' => 'required|string|max:10',
        'miejscowosc' => 'required|string|max:100',
        'adres' => 'required|string|max:255',
        'nip' => 'required|string|max:15|unique:firmy,nip',
        'miejsce_wystawienia' => 'required|string|max:100',
        'telefon' => 'required|string|max:20',
        'email' => 'required|email|max:100',
        'waluta_domyslna' => 'required|string|max:3',
        'prefix_faktury' => 'required|string|max:10',
    ]);
    
    if ($validator->fails()) {
        return $this->sendError('Błąd walidacji.', $validator->errors(), 422);
    }
    
    $klient = new Firma($request->all());
    $klient->typ = 'klient';
    $klient->firma_ksiegowa_id = $user->firma_id;
    $klient->save();
    
    return $this->sendResponse($klient, 'Klient dodany pomyślnie.');
}

    /**
     * Wyświetl listę firm
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $firmy = Firma::all();
        return $this->sendResponse($firmy, 'Firmy pobrane pomyślnie');
    }

    /**
     * Zapisz nową firmę
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nazwa' => 'required|max:100',
            'nazwa_pelna' => 'required|max:255',
            'kod_pocztowy' => 'required|max:10',
            'miejscowosc' => 'required|max:100',
            'adres' => 'required|max:255',
            'nip' => 'required|max:15|unique:firmy',
            'miejsce_wystawienia' => 'required|max:100',
            'telefon' => 'required|max:20',
            'email' => 'required|email|max:100',
            'waluta_domyslna' => 'required|max:3',
            'prefix_faktury' => 'required|max:10',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors());
        }

        $firma = Firma::create($request->all());
        return $this->sendResponse($firma, 'Firma utworzona pomyślnie');
    }

    /**
     * Wyświetl szczegóły firmy
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $firma = Firma::find($id);

        if (is_null($firma)) {
            return $this->sendError('Firma nie istnieje');
        }

        return $this->sendResponse($firma, 'Firma pobrana pomyślnie');
    }

    /**
     * Aktualizuj firmę
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $firma = Firma::find($id);

        if (is_null($firma)) {
            return $this->sendError('Firma nie istnieje');
        }

        $validator = Validator::make($request->all(), [
            'nazwa' => 'required|max:100',
            'nazwa_pelna' => 'required|max:255',
            'kod_pocztowy' => 'required|max:10',
            'miejscowosc' => 'required|max:100',
            'adres' => 'required|max:255',
            'nip' => 'required|max:15|unique:firmy,nip,' . $id,
            'miejsce_wystawienia' => 'required|max:100',
            'telefon' => 'required|max:20',
            'email' => 'required|email|max:100',
            'waluta_domyslna' => 'required|max:3',
            'prefix_faktury' => 'required|max:10',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors());
        }

        $firma->update($request->all());
        return $this->sendResponse($firma, 'Firma zaktualizowana pomyślnie');
    }

    /**
     * Usuń firmę
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $firma = Firma::find($id);

        if (is_null($firma)) {
            return $this->sendError('Firma nie istnieje');
        }

        $firma->delete();
        return $this->sendResponse([], 'Firma usunięta pomyślnie');
    }
}