<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Validator;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $statusCode = 500;
    private $status = "error";
    private $message = "";
    private $data = null;


    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();
        if($user) {
            if(\Hash::check($request->password, $user->password)) {
                $this->data['token'] = $user->createToken('nApp')->accessToken;
                $this->data['name'] = $user->name;
                $this->status = "success";
                $this->message = "Login success";
                $this->statusCode = 200;
            } else {
                $this->message = "Login failed, email and password doesn't match";
                $this->statusCode = 401;
            }
        } else {
            $this->message = "Login failed, your email is wrong";
            $this->statusCode = 401;
        }

        return response()->json([
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data
        ], $this->statusCode);
    }

    public function register(RegisterRequest $request) 
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = \Hash::make($request->password);
        $user->role_id = 2;
        $user->save();

        $this->data['token'] = $user->createToken('nApp')->accessToken;
        $this->data['name'] = $user->name;
        $this->status = 201;
        $this->message = "Register Success";

        return response()->json([
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data
        ],$this->statusCode);

    }

    public function logout()
    {
        $user = Auth::user()->token();
        $user->revoke();
        $this->message = "Logout Success";
        $this->status = "success";
        $this->statusCode = 204;

        return response()->json([
            'status' => $this->status,
            'message' => $this->message,

        ], $this->statusCode);
    }
}
