<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;


class AuthController extends Controller
{
    public function register(Request $request){

        try{
        $validatedata = $request->validate([
            'name' => 'required|string',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8'
        ]);

       $user=  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user'
        ]);

        return response()->json([
        'status' => 'success',
        'message' => 'User registered successfully',
        'user' => $user
        ]);


        }catch(\Exception $e){
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }



    }

    public function login(Request $request){
        try{

            $validatedata = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email',$request->email)->first();
        
        if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json([
                'message' => 'Invalid credential'
            ]);
        }

         Auth::login($user);

        $token = $user->createToken('api_token')->plainTextToken;
        return response()->json([
                'message' => 'Login success',
                'token' => $token,
                'user' => $user
            ]);

        }catch(\Exception $e){

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout Success'
        ]);
    }
}
