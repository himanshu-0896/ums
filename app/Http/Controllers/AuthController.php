<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;


class AuthController extends Controller
{
    public function register(Request $request){

        // return response()->json([
        //     'message' => $request->all()
        // ]);
        try{
        $validatedata = $request->validate([
            'name' => 'required|string',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
            'profile_pic' => 'nullable|mimes:jpg,jpeg,png,webp',
            'address' => 'required|string',
            'phone_number' => 'required|max:10'

        ]);

        if($request->hasFile('profile_pic')){
            $file = $request->file('profile_pic');
            $extension = $file->getClientOriginalExtension();
            $filename = time() .".". $extension;  
            $file->move(public_path('uploads/images',$filename));
        }else{
            $filename =  'default.png';
        }

       $user=  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'profile_pic' => 'uploads/images/' . $filename,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
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

    public function profile(Request $request){
        return $request->user();
    }

    public function update(Request $request){

        $user = $request->user();
        try{

        $validatedata = $request->validate([
            'name' => 'required|string',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
            'profile_pic' => 'nullable|mimes:jpg,jpeg,png,webp',
            'address' => 'required|string',
            'phone_number' => 'required|max:10'

        ]);

        if($request->hasFile('profile_pic')){
            $file = $request->file('profile_pic');
            $extension = $file->getClientOriginalExtension();
            $filename = time() .".". $extension;  
            $file->move(public_path('uploads/images',$filename));
        }else{
            $filename =  'default.png';
        }

         User::where('id',$user->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'profile_pic' => 'uploads/images/' . $filename,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'role' => 'user'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User Updated successfully',
            'user' => $user->fresh()
        ]);


        }catch(\Exception $e){
            return response()->json([
            'status' => 'Failed',
            'message' => $e->getmessage(),
        ]);

        }
    }
}
