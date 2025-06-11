<?php

namespace App\Http\Controllers\API;

use App\Models\Odbiorca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OdbiorcaController extends BaseController
{
    /**
     * Wyświetl listę odbiorców
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $odbiorcy = Odbiorca::orderBy('nazwa')->get();
        return $this->sendResponse($odbiorcy, 'Odbiorcy pobrani pomyślnie');
    }

    /**
     * Zapisz nowego odbiorcę
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nazwa' => 'required|string|max:100',
            'nazwa_pelna' => 'required|string|max:255',
            'kod_pocztowy' => 'required|string|max:10',
            'miejscowosc' => 'required|string|max:100',
            'adres' => 'required|string|max:255',
            'nip' => 'nullable|string|max:15',
            'telefon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'kraj' => 'required|string|max:100',
            'typ_odbiorcy' => 'required|in:krajowy,ue,pozaue'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors(), 422);
        }

        $odbiorca = Odbiorca::create($request->all());
        return $this->sendResponse($odbiorca, 'Odbiorca utworzony pomyślnie');
    }

    /**
     * Wyświetl szczegóły odbiorcy
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $odbiorca = Odbiorca::find($id);

        if (is_null($odbiorca)) {
            return $this->sendError('Odbiorca nie istnieje');
        }

        return $this->sendResponse($odbiorca, 'Odbiorca pobrany pomyślnie');
    }

    /**
     * Aktualizuj odbiorcę
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $odbiorca = Odbiorca::find($id);

        if (is_null($odbiorca)) {
            return $this->sendError('Odbiorca nie istnieje');
        }

        $validator = Validator::make($request->all(), [
            'nazwa' => 'required|string|max:100',
            'nazwa_pelna' => 'required|string|max:255',
            'kod_pocztowy' => 'required|string|max:10',
            'miejscowosc' => 'required|string|max:100',
            'adres' => 'required|string|max:255',
            'nip' => 'nullable|string|max:15',
            'telefon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'kraj' => 'required|string|max:100',
            'typ_odbiorcy' => 'required|in:krajowy,ue,pozaue'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors(), 422);
        }

        $odbiorca->update($request->all());
        return $this->sendResponse($odbiorca, 'Odbiorca zaktualizowany pomyślnie');
    }

    /**
     * Usuń odbiorcę
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $odbiorca = Odbiorca::find($id);

        if (is_null($odbiorca)) {
            return $this->sendError('Odbiorca nie istnieje');
        }

        $odbiorca->delete();
        return $this->sendResponse([], 'Odbiorca usunięty pomyślnie');
    }
}