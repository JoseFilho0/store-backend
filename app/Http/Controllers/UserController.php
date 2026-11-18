<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),    
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8']
            ], [
                'name.required' => 'O campo nome é obrigatório.',
                'name.string' => 'O campo nome deve ser uma string.',
                'name.max' => 'O campo nome deve ter no máximo 255 caracteres.',
                'email.required' => 'O campo email é obrigatório.',
                'email.string' => 'O campo email deve ser uma string.',
                'email.max' => 'O campo email deve ter no máximo 255 caracteres.',
                'email.email' => 'O campo email deve ser um endereço de email válido.',
                'email.unique' => 'O email informado já está em uso.',
                'password.required' => 'O campo senha é obrigatório.',
                'password.string' => 'O campo senha deve ser uma string.',
                'password.min' => 'A senha deve ter no mínimo 8 caracteres.'
            ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'user' => null
            ], 400);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'error' => null,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string']
            ], [
                'email.required' => 'O campo email é obrigatório.',
                'email.string' => 'O campo email deve ser uma string.',
                'email.email' => 'O campo email deve ser um endereço de email válido.',
                'password.required' => 'O campo senha é obrigatório.',
                'password.string' => 'O campo senha deve ser uma string.'
            ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Usuário ou senha inválidos.',
                'user' => null
            ], 400);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'error' => 'Usuário ou senha inválidos.',
                'user' => null
            ], 400);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'error' => null,
            'token' => $token,
        ]);
    }

    public function createAddress(Request $request)
    {
        $validator = Validator::make(
            $request->all(),       
            [
                'zipcode' => ['required', 'string', 'max:20'],
                'street' => ['required', 'string', 'max:255'],
                'number' => ['required', 'string', 'max:20'],
                'city' => ['required', 'string', 'max:100'],
                'state' => ['required', 'string', 'max:100'],
                'country' => ['required', 'string', 'max:100'],
                'complement' => ['sometimes', 'nullable', 'string', 'max:255'],
            ],
            [
                'zipcode.required' => 'O CEP é obrigatório.',
                'zipcode.string' => 'O CEP deve ser um texto válido.',
                'zipcode.max' => 'O CEP deve ter no máximo 20 caracteres.',
                'street.required' => 'O logradouro é obrigatório.',
                'street.string' => 'O logradouro deve ser um texto válido.',
                'street.max' => 'O logradouro deve ter no máximo 255 caracteres.',
                'number.required' => 'O número do endereço é obrigatório.',
                'number.string' => 'O número do endereço deve ser um texto válido.',
                'number.max' => 'O número do endereço deve ter no máximo 20 caracteres.',
                'city.required' => 'A cidade é obrigatória.',
                'city.string' => 'A cidade deve ser um texto válido.',
                'city.max' => 'A cidade deve ter no máximo 100 caracteres.',
                'state.required' => 'O estado é obrigatório.',
                'state.string' => 'O estado deve ser um texto válido.',
                'state.max' => 'O estado deve ter no máximo 100 caracteres.',
                'country.required' => 'O país é obrigatório.',
                'country.string' => 'O país deve ser um texto válido.',
                'country.max' => 'O país deve ter no máximo 100 caracteres.',
                'complement.string' => 'O complemento deve ser um texto válido.',
                'complement.max' => 'O complemento deve ter no máximo 255 caracteres.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'address' => null
            ], 400);
        }

        $user = Auth::user();

        $address = Address::create([
            'user_id' => $user->id,
            'zipcode' => $request->input('zipcode'),
            'street' => $request->input('street'),
            'number' => $request->input('number'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'country' => $request->input('country'),
            'complement' => $request->input('complement'),
        ]);

        return response()->json([
            'error' => null,
            'address' => [
                'id' => $address->id,
                'zipcode' => $address->zipcode,
                'street' => $address->street,
                'number' => $address->number,
                'city' => $address->city,
                'state' => $address->state,
                'country' => $address->country,
                'complement' => $address->complement
            ]
        ]);
    }

    public function getAddresses(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $addresses = $user->addresses()->get();

        return response()->json([
            'error' => null,
            'addresses' => $addresses
        ]);
    }
}
