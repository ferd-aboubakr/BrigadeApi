<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  
    public function register(Request $request) {
        // 1. Validate (including dietary profile fields)
        // 2. Create User
        // 3. Create & Return Token

        $request->headers->set('Accept', 'application/json');

        $input = $request->validate([
            'name'=> 'required|string|max:255',
            'email'=> 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            'role' => 'nullable|string',
            'dietary_tags' => 'nullable|array'
        ]);


        $user = User::create(
            [
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'role' => $input['role'] ?? '',
                'dietary_tags' => $input['dietary_tags'] ?? [],
            ]
        );

          $token =$user->createToken('auth-token')->plainTextToken;
          return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }




    
    public function login(Request $request) {
        // 1. Validate email/password
        // 2. Verify Hash
        // 3. Create & Return Token

         $request->validate(            [
            'email'=> 'required|string|email|exists:users,email',
            'password' => 'required|string|min:8',
            ]);

          $user = User::where('email', $request->email)->first() ;

          if(!$user || !Hash::check($request->password, $user->password))
               {
                 return response()->json(['message' => 'invalid credentials'],401);
               }

        $token =$user->createToken('auth-token')->plainTextToken;


        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);



        
    }


    // POST /api/logout
    public function logout(Request $request) {

        // 1. Revoke current access token
        $request->user()->currentAccessToken()->delete();
    
    return response()->json( ['message' =>'log out success !'],200);
    }
}



















