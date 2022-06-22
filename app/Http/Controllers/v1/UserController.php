<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\TokenRepository;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(UserRegisterRequest $request)
    {
        $password_hashed = Hash::make($request['password']);
        $user = User::create([
            "email"=>$request->email,
            "name"=>$request->name,
            "password"=>$password_hashed,
        ]);
        $accessToken = $user->createToken('AccessToken')->accessToken;

        return response(['user'=>$user, 'token'=>$accessToken], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $tokenID = $request->user()->token()->id;
        $tokenRepository = app(TokenRepository::class);
        $tokenRepository->revokeAccessToken($tokenID);
        return($tokenID);
    }

     /** @var \App\Models\User $user **/

    public function login(Request $request){
        $login = $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if (!Auth::attempt($login)){
            abort(227, "Qayta urinib ko'ring");
        }

        $user = Auth::getprovider()->retrieveByCredentials($login);
        $accessToken = $user->createToken('AccessToken')->accessToken;

        return response()->json(['user'=>$user, 'token'=>$accessToken]); 
    }
}
