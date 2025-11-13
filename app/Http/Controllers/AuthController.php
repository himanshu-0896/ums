<?php

namespace App\Http\Controllers;
use App\Models\User;

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
}
