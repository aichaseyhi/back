<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware(['role:admin|superadmin']);
    }
    
    public function index()
    {
        $query = User::query();
        $query->orderBy('name', 'asc');

        $users = $query->get();

        return response()->json($users, 200);
    }

    public function store(Request $request)
    {
        //valdiate
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => ['required', 'regex:/^[0-9]{8}$/'],
            'password' => 'required|string|min:6|max:24|',
            'role' => 'required|string',
            'birthday' => 'required|date',
            'sexe' => ['required', 'in:male,female'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                "status" => 400
            ]);
        }
        // $data = $request->only('name', 'email', 'phone', 'password','role');


        $user = new User();


        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->birthday = Carbon::createFromFormat('d/m/Y', $request->birthday)->format('Y-m-d');
        $user->sexe = $request->sexe;
        $user->status = $request->status;
        $user->save();
        $user->assignRole($request->role);

       /* if ($request->role == 'provider-intern'){
            $store = new Store();
            $store->name = $request->nameboutique;
            $store->provider_id = $user->id;
            $store->save();
        }*/

        return response()->json([
            'message' => "successfully registered",
            "status" => Response::HTTP_CREATED
        ]);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(['message' => 'utilisateur introuvable'], 404);
        }
        return response()->json(User::find($id), 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(['message' => 'utilisateur introuvable'], 404);
        }

        // Supprimer l'utilisateur
        $user->delete();

        return response(null, 204);
    }

 
    public function getUsersByRole($Role)
    {
        $userRole = User::role($Role)->get();
        return response()->json( $userRole);
    }

    public function filterUser(Request $request)
    {
        $query = User::query();

    // Filtrage par nom
    if ($request->has('name')) {
        $query->where('name', 'like', '%' . $request->input('name') . '%');
    }

    // Filtrage par e-mail
    if ($request->has('email')) {
        $query->where('email', $request->input('email'));
    }

    $users = $query->get();

    return response()->json($users, 200);
    }

}
