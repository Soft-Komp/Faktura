<?php

namespace App\Http\Controllers\API;

use App\Models\Artykul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArtykulController extends BaseController
{
    /**
     * Wyświetl listę artykułów
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $artykuly = Artykul::all();
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
            'nazwa' => 'required|max:100',
            'kod_artykulu' => 'required|max:20|unique:artykuly',
            'jednostka' => 'required|max:10',
            'stawka_vat' => 'required|numeric|between:0,100',
            'cena_netto' => 'required|numeric|min:0',
            'cena_brutto' => 'required|numeric|min:0',
            'opis' => 'nullable|max:255',
            'aktywny' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors());
        }

        $artykul = Artykul::create($request->all());
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
        $artykul = Artykul::find($id);

        if (is_null($artykul)) {
            return $this->sendError('Artykuł nie istnieje');
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
        $artykul = Artykul::find($id);

        if (is_null($artykul)) {
            return $this->sendError('Artykuł nie istnieje');
        }

        $validator = Validator::make($request->all(), [
            'nazwa' => 'required|max:100',
            'kod_artykulu' => 'required|max:20|unique:artykuly,kod_artykulu,' . $id,
            'jednostka' => 'required|max:10',
            'stawka_vat' => 'required|numeric|between:0,100',
            'cena_netto' => 'required|numeric|min:0',
            'cena_brutto' => 'required|numeric|min:0',
            'opis' => 'nullable|max:255',
            'aktywny' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors());
        }

        $artykul->update($request->all());
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

        $artykul->delete();
        return $this->sendResponse([], 'Artykuł usunięty pomyślnie');
    }
}