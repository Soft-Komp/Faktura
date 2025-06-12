<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Firma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    /**
     * Wyświetl listę użytkowników (tylko admin)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::with('firma')->orderBy('name')->get();
        return $this->sendResponse($users, 'Użytkownicy pobrani pomyślnie');
    }

    /**
     * Wyświetl szczegóły użytkownika
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::with('firma')->find($id);

        if (is_null($user)) {
            return $this->sendError('Użytkownik nie istnieje');
        }

        return $this->sendResponse($user, 'Użytkownik pobrany pomyślnie');
    }

    /**
     * Utwórz nowego użytkownika (tylko admin)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'firma_id' => 'nullable|exists:firmy,id',
            'rola' => 'required|in:admin,ksiegowy,klient'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'firma_id' => $request->firma_id,
            'rola' => $request->rola
        ]);

        $user->load('firma');

        return $this->sendResponse($user, 'Użytkownik utworzony pomyślnie');
    }

    /**
     * Aktualizuj użytkownika
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('Użytkownik nie istnieje');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'firma_id' => 'nullable|exists:firmy,id',
            'rola' => 'required|in:admin,ksiegowy,klient'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors(), 422);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->firma_id = $request->firma_id;
        $user->rola = $request->rola;
        
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        $user->load('firma');

        return $this->sendResponse($user, 'Użytkownik zaktualizowany pomyślnie');
    }

    /**
     * Usuń użytkownika
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('Użytkownik nie istnieje');
        }

        // Nie pozwól usunąć siebie
        if ($user->id === auth()->user()->id) {
            return $this->sendError('Nie możesz usunąć swojego konta');
        }

        $user->delete();
        return $this->sendResponse([], 'Użytkownik usunięty pomyślnie');
    }

    /**
     * Przypisz użytkownika do firmy
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignToFirma(Request $request, $id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('Użytkownik nie istnieje');
        }

        $validator = Validator::make($request->all(), [
            'firma_id' => 'required|exists:firmy,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors(), 422);
        }

        $user->firma_id = $request->firma_id;
        $user->save();
        $user->load('firma');

        return $this->sendResponse($user, 'Użytkownik przypisany do firmy pomyślnie');
    }

    /**
     * Odepnij użytkownika od firmy
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detachFromFirma($id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('Użytkownik nie istnieje');
        }

        $user->firma_id = null;
        $user->save();
        $user->load('firma');

        return $this->sendResponse($user, 'Użytkownik odepnięty od firmy pomyślnie');
    }

    /**
     * Zmień rolę użytkownika
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeRole(Request $request, $id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('Użytkownik nie istnieje');
        }

        $validator = Validator::make($request->all(), [
            'rola' => 'required|in:admin,ksiegowy,klient'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors(), 422);
        }

        // Nie pozwól zmienić roli sobie
        if ($user->id === auth()->user()->id) {
            return $this->sendError('Nie możesz zmienić swojej roli');
        }

        $user->rola = $request->rola;
        $user->save();
        $user->load('firma');

        return $this->sendResponse($user, 'Rola użytkownika zmieniona pomyślnie');
    }
}