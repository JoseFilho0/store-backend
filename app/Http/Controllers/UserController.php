<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
}
