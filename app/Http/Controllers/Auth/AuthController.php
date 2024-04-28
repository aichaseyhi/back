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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;
use App\Mail\forgetPasswordCode;
use App\Models\verification_code;

class AuthController extends Controller
{

    
    public  function register(Request $request){
        $rules = [
            'name'=>'required',
            'phone'=>['required', 'regex:/^[0-9]{8}$/'],
            'email'=>'required|email|unique:users,email|',
            'password'=>'required|min:6|max:24|',
            //'birthday' => 'nullable|date',
           // 'sexe' => ['nullable', 'in:male,female'],
            'role'=>'required',
            'image'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'acountLink'=> 'nullable|string',
            'street'=> 'nullable|string',
            'city'=> 'nullable|string',
            'post_code'=> ['nullable', 'regex:/^[0-9]{4}$/'],
            'CIN'=> ['nullable', 'regex:/^[0-9]{8}$/'],
            'TAXNumber'=> 'nullable|regex:/^[0-9]{8}$/',
            'companyName'=> 'nullable|string',
            'companyUnderConstruction'=> 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                "status" => 400
            ]);
        }
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
       // $user->birthday = Carbon::createFromFormat('d/m/Y', $request->birthday)->format('Y-m-d');
       // $user->sexe = $request->sexe;
        $user->image = $imageName ? env('APP_URL') . '/storage/users/' . $imageName : null;
        $user->acountLink = $request->acountLink;
        $user->street = $request->street;
        $user->city = $request->city;
        $user->post_code = $request->post_code;
        $user->CIN = $request->CIN;
        $user->companyName = $request->companyName;
        $user->companyUnderConstruction = $request->companyUnderConstruction;
        if ($request->companyUnderConstruction == false) {
            $user->TAXNumber  = $request->TAXNumber;
        } 

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
   

//forget password section >>>
    // generate random code 
    function randomcode($_length)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $_length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    // send random code to virif mail
    public function forgetPassWord(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    if (!User::where('email', $value)->exists() ) {
                        $fail('The selected email is invalid.');
                    }
                },
            ],
        ]);


        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                "status" => 400
            ]);
        }

        $user = User::where('email', $request->email)->first();
        $code = self::randomcode(4);
        // make old codes expired
        verification_code::where(["email" => $request->email, "status" => "pending"])->update(["status" => "expired"]);
        if ($user) {
            $data = [
                "email" => $request->email,
                "name" => $user->name,
                "code" => $code,
                "subject" => "forget password",
            ];
            Mail::to($data["email"])->send(new forgetPasswordCode($data));
            $verifTable = new verification_code();
            $verifTable->email = $request->email;
            $verifTable->code = $code;
            $verifTable->status = "pending";
            $verifTable->save();

            return response()->json([
                'status' => 'success',
                'data' => $data,
                'new code' => $verifTable,
            ]);
        }
    } 
    public function changePassword(Request $request)
    {
        $user = User::find(auth()->user()->id);

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        // Chiffrer le nouveau mot de passe
        $password_hashed = Hash::make($request->password);

        // Mettre à jour le mot de passe dans la base de données
        $user->password = $password_hashed;
        $user->save();

        return response()->json(['message' => 'Mot de passe mis à jour avec succès']);
    }

    public function verifCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    if (!User::where('email', $value)->exists() ) {
                        $fail('The selected email is invalid.');
                    }
                },
            ],
            'code' => [
                "required",
                "string",
                "min:4",
                "max:4",
                "exists:verification_codes,code"
            ]
        ]);


        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                "status" => 400
            ]);
        }
        $code = $request->code;
        $dataBaseCode = verification_code::where(["email" => $request->email, "status" => "pending", "code" => $code])->orderBy('created_at', 'desc')->first();

        if ($dataBaseCode) {
            $dataBaseCode->status = "used";
            $dataBaseCode->save();
            return response()->json([
                'message' => "verification success",
                "status" => 200,
                "code" => $dataBaseCode
            ]);
        } else {
            return response()->json([
                "message" => "invalide verification code",
                "status" =>  406 // not acceptable == 406
            ]);
        }
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
