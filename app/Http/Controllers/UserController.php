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
    public $data = null;


    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();
        if($user) {
            if(\Hash::check($request->password, $user->password)) {
                $this->data['token'] = $user->createToken('nApp')->accessToken;
                $this->status = "success";
                $this->message = "Login success";
            } else {
                $this->message = "Login failed, email and password doesn't match";
            }
        } else{
            $this->message = "Login failed, your email is wrong";
        }

        return response()->json([
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data
        ], $this->successStatus);
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
