<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;

class UserAccessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get("search");

        $users = User::where('name', 'like', "%".$search."%")->orderBy("id","desc")->paginate(25);
        return response()->json([
            "total" => $users->total(),
            "users" => $users->map(function ($user){
                return [
                    "id" => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password,

                    'surname' => $user->surname,
                    'full_name' => $user->name.' '.$user->surname,
                    'phone' => $user->phone,
                    'role_id' => $user->role_id,
                    'role' => $user->role,
                    'roles' => $user->roles,
                    'sucursal_id' => $user->sucursal_id,
                    'type_document' => $user->type_document,
                    'n_document' => $user->n_document,
                    'address' => $user->address,
                    'gender' => $user->gender,
                    'avatar' => $user->avatar ? env('APP_URL')."storage/".$user->avatar : 'https://cdn-icons-png.flaticon.com/128/3135/3135715.png',
                    'created_format_at' => $user->created_at->format('Y-m-d h:i A'),
                ];
            }),
        ]);
    }

    public function config(){
        return response()->json([
            "roles" => Role::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $USER_EXISTS = User::where('email', $request->email)->first();
        if($USER_EXISTS){
            return response()->json([
                "message" => 403,
                "message_text" => "EL USUARIO YA EXISTE"
            ]);
        }

        if($request->hasFile("imagen")){
            $path = Storage::putFile("users",$request->file("imagen"));
            $request->request->add(["avatar" => $path]);
        }

        if($request->password){
            $request->request->add(["password" => bcrypt($request->password)]);
        }

        $role = Role::findOrFail($request->role_id);
        $user = User::create($request->all());
        $user->assignRole($role);

        return response()->json([
            "message" => 200,
            "user" => [
                "id" => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,

                'surname' => $user->surname,
                'full_name' => $user->name.' '.$user->surname,
                'phone' => $user->phone,
                'role_id' => $user->role_id,
                'role' => $user->role,
                'roles' => $user->roles,
                'sucursal_id' => $user->sucursal_id,
                'type_document' => $user->type_document,
                'n_document' => $user->n_document,
                'address' => $user->address,
                'gender' => $user->gender,
                'avatar' => $user->avatar ? env('APP_URL')."storage/".$user->avatar : 'https://cdn-icons-png.flaticon.com/128/3135/3135715.png',
                'created_format_at' => $user->created_at->format('Y-m-d h:i A'),
            ],
            "message_text" => "USUARIO CREADO EXITOSAMENTE"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $USER_EXISTS = User::where('email', $request->email)->where('id', '<>', $id)->first();
        if($USER_EXISTS){
            return response()->json([
                "message" => 403,
                "message_text" => "EL USUARIO YA EXISTE"
            ]);
        }

        $user = User::findOrFail($id);
        if($request->hasFile("imagen")){
            if($user->avatar){
                Storage::delete($user->avatar);
            }
            $path = Storage::putFile("users",$request->file("imagen"));
            $request->request->add(["avatar" => $path]);
        }

        if($request->password){
            $request->request->add(["password" => bcrypt($request->password)]);
        }


        if($request->role_id != $user->role_id){
            //ASIGNACIÓN DEL VIEJO ROL
            $role_old = Role::findOrFail($user->role_id);
            $user->removeRole($role_old);

            //ASIGNACIÓN DEL NUEVO ROL
            $role = Role::findOrFail($request->role_id);
            $user->assignRole($role);
        }

        $user->update($request->all());
        
        return response()->json([
            "message" => 200,
            "user" => [
                "id" => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,

                'surname' => $user->surname,
                'full_name' => $user->name.' '.$user->surname,
                'phone' => $user->phone,
                'role_id' => $user->role_id,
                'role' => $user->role,
                'roles' => $user->roles,
                'sucursal_id' => $user->sucursal_id,
                'type_document' => $user->type_document,
                'n_document' => $user->n_document,
                'address' => $user->address,
                'gender' => $user->gender,
                'avatar' => $user->avatar ? env('APP_URL')."storage/".$user->avatar : 'https://cdn-icons-png.flaticon.com/128/3135/3135715.png',
                'created_format_at' => $user->created_at->format('Y-m-d h:i A'),
            ],
            "message_text" => "USUARIO CREADO EXITOSAMENTE"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if($user->avatar){
            Storage::delete($user->avatar);
        }
        $user->delete();

        return response()->json([
            "message" => 200,
            "message_text" => "USUARIO ELIMINADO EXITOSAMENTE"
        ]);
    }
}
