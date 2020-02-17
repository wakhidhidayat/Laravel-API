<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public $successStatus = 200;
    public $unauthorizedStatus = 401;
    public $status = "error";
    public $message = "";


    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();
        if($user) {
            if(\Hash::check($request->password, $user->password)) {
                $data['token'] = $user->createToken('nApp')->accessToken;
                $status = "success";
                $message = "Login success";
            } else {
                $message = "Login failed, password doesn't match";
            }
        } else {
            $message = "Login failed, your email is wrong";
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], 200);
        // if(Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
        //     $user = Auth::user();
        //     $success['token'] = $user->createToken('nApp')->accessToken;
        //     return response()->json([
        //         'success' => $success
        //     ], $this->successStatus);
        // } else {
        //     return response()->json([
        //         'error' => 'Unauthorized'
        //     ], $this->unauthorizedStatus);
        // }
    }

    public function register(RegisterRequest $request) 
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = \Hash::make($request->password);
        $user->role_id = 2;
        $user->save();

        $success['token'] = $user->createToken('nApp')->accessToken;
        $success['name'] = $user->name;

        return response()->json([
            'success' => $success,
        ],$this->successStatus);

    }
}
