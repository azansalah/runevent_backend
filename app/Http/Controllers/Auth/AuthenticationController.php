<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Models\User;
use Firebase\JWT\JWT;

class AuthenticationController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function register()
    {
        return Hash::make('user.admin');
    }

    public function logIn()
    {
        $validator = Validator::make($this->request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->messages();
            return responder()->error()->data([
                'validate' => $messages,
            ])->respond(400);
        }

        $username = $this->request->input('username');
        $password = $this->request->input('password');
        $user = User::where('username', $username)->first();
        if($user && Hash::check($password, $user->password)) {
            
            $token = $this->jwt($user);
            $result = [
                'name' => "$user->f_name $user->l_name",
                'username' => $user->username,
                'token' => $token
            ];

            $data = [
                'result' => $result
            ];

            return responder()->success($data)->respond(200);
            
        }else {
            return responder()->error()->respond(400);
        }
    }

    protected function jwt($user)
    {
        $payload = [
            'iss' => "run event app",
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + env('JWT_EXPIRE_HOUR') * 60 * 60, // Expiration time
        ];

        return JWT::encode($payload, env('JWT_SECRET'));
    }
}