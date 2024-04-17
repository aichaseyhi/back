<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
use JWTAuth;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{

    
    public  function register(Request $request){
        $request->validate([
            'name'=>'required',
            'phone'=>['required', 'regex:/^[0-9]{8}$/'],
            'email'=>'required|email|unique:users,email|',
            'password'=>'required|min:6|max:24|',
            'birthday' => 'nullable|date',
            'sexe' => ['nullable', 'in:male,female'],
            'role'=>'required',
            'image'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/users'), $imageName);
        }

        $user = new User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->birthday = Carbon::createFromFormat('d/m/Y', $request->birthday)->format('Y-m-d');
        $user->sexe = $request->sexe;
        $user->image = $imageName ? env('APP_URL') . '/storage/users/' . $imageName : null;
       

        $user->save();
        $user->assignRole($request->role);

      
        
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
        $user = User::where('email', $request->email)->first();
       

        if ($user->status != 'ACTIVE') {
                return response()->json([
                    "message" => 'Your account is not active',
                    "status" => 401
                ]);
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
    public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        'email' => [
            'required',
            'string',
            Rule::unique('users')->ignore($id),
            'email'
        ],
        'phone' => ['required', 'regex:/^[0-9]{8}$/'],
        'birthday' => ['required', 'date'],
        'sexe' => ['required', 'in:male,female'],
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
            "status" => 400
        ]);
    }

    $user = User::find($id);

    if (is_null($user)) {
        return response()->json(
            [
                'message' => 'utilisateur introuvable',
                "status" => "404"
            ]
        );
    }

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('storage/users'), $imageName);
        $user->image = env('APP_URL') . '/storage/users/' . $imageName;
    }

    $user->name = $request->name;
    $user->phone = $request->phone;
    $user->email = $request->email;
    $user->birthday = Carbon::createFromFormat('d/m/Y', $request->birthday)->format('Y-m-d');
    $user->sexe = $request->sexe;

    $user->save();

    return response()->json([
        "message" => "Updated Successfully",
        "status" => 200,
    ]);
}

    // public function update(Request $request, $id)
    // {
    //     //valdiate
    //     // $rules = [];
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string',
    //         'email' =>  [
    //             'required',
    //             'string',
    //             Rule::unique('users')->ignore($id),
    //             'email'
    //         ],
    //         'phone' => ['required', 'regex:/^[0-9]{8}$/'],
    //         'birthday' => ['required', 'date'],
    //         'sexe' => ['required', 'in:male,female'],
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             $validator->errors(),
    //             "status" => 400
    //         ]);
    //     }
    //     $user = User::find($id);
    //     if (is_null($user)) {
    //         return response()->json(
    //             [
    //                 'message' => 'utilisateur introuvable',
    //                 "status" => "404"
    //             ]
    //         );
    //     }
    //     $user->birthday = Carbon::createFromFormat('m/d/Y', $request->birthday)->format('Y-m-d');
        
    //     $user->update($request->only('name', 'email', 'phone', 'birthday', 'sexe', 'status'));
    //     return response()->json([
    //         "message" => "Updated Successefully",
    //         "status" => 200,
    //     ]);
    // }

}
