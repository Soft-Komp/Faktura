<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Firma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseController
{
    /**
     * Rejestracja nowego użytkownika
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
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

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendResponse([
            'user' => $user->load('firma'),
            'token' => $token,
            'token_type' => 'Bearer'
        ], 'Użytkownik zarejestrowany pomyślnie');
    }

    /**
     * Logowanie użytkownika
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Nieprawidłowe dane logowania', [], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendResponse([
            'user' => $user->load('firma'),
            'token' => $token,
            'token_type' => 'Bearer'
        ], 'Zalogowano pomyślnie');
    }

    /**
     * Wylogowanie użytkownika
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse([], 'Wylogowano pomyślnie');
    }

    /**
     * Pobranie profilu użytkownika
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load('firma');
        
        return $this->sendResponse($user, 'Profil użytkownika pobrany pomyślnie');
    }

    /**
     * Aktualizacja profilu użytkownika
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Błąd walidacji', $validator->errors(), 422);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return $this->sendResponse($user->load('firma'), 'Profil zaktualizowany pomyślnie');
    }
}