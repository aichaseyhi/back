<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
use JWTAuth;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public  function register(Request $request){
        $request->validate([
            'name'=>'required',
            'phone'=>'required', 'regex:/^[0-9]{8}$/',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:6|max:24|',
            'birthday' => 'required|date|date_format:Y-m-d',
            'sexe' => ['required', 'in:male,female'],
            'status' => 'required',
            'role'=>'required',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        $user->save();
        $user->assignRole($request->role);

        if ($request->role == 'provider-intern'){
            $store = new Store();
            $store->name = $request->nameboutique;
            $store->provider_id = $user->id;
            $store->save();
        }
        
        return response()->json('User Created');
    }
   
    public function login(Request $request)
    {
        $creds = $request->only(['email','password']);
        if (!$token=auth()->attempt($creds)){
            return response()->json([
                'success'=>false,
                'message'=>'information incorrecte'
            ],Response::HTTP_UNAUTHORIZED);
        }
        return response()->json([
            'success'=>true,
            'token'=>$token,
            'user'=>Auth::user()
        ],Response::HTTP_OK);

    }

    public function logout(Request $request){
        try {
            FacadesJWTAuth::invalidate(FacadesJWTAuth::parseToken($request->token));
            return response()->json([
                "success"=>true,
                "message"=>"logout success"
            ]);
        }catch (Exception $exception){
            return response()->json([
                "success"=>false,
                "message"=>"".$exception
            ]);
        }
    }
    public function user()
    {
        return response()->json([
            'user' => Auth::user(),
        ]);
    }
}
