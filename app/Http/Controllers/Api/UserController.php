<?php

namespace App\Http\Controllers\Api;

use DB;
use Auth;
use Response;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function users()
    {
        $users = User::orderBy('name')->paginate(15);

        return UserResource::collection($users);
    }

    public function user($id)
    {
        $user = User::where('id',$id)->first();

        if( count($user) <=0 ){
            return Response::json( [
                'status' => [
                    "code" => 400,
                    "description" => 'Bad Request!'
                ]
                ], 400 );
        }

        return (new UserResource($user))
                ->additional([
                    'status' => [
                        "code" => 200,
                        "description" => 'OK'
                    ]
                ])->response()->setStatusCode(200);
    }

    public function login(Request $request)
    {
        // return $request->all();
        if( ! Auth::attempt( [ "email" => $request->email, "password" => $request->password ] ) ){
            return Response::json( [
                'status' => [
                    "code" => 401,
                    "description" => 'Credential Is Wrong'
                ]
                ], 401 );
        }

        $loggedUser = User::find(Auth::user()->id);

        return (new UserResource($loggedUser))
                ->additional([
                    'status' => [
                        "code" => 202,
                        "description" => 'OK'
                    ]
                ])->response()->setStatusCode(202);
    }

    public function register(Request $request)
    {
        // return $request->all();
        $this->validate($request,[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:3',
        ]);

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'api_token' => bcrypt($request->email),
        ]);

        return (new UserResource($newUser))
                ->additional([
                    'status' => [
                        "code" => 201,
                        "description" => 'OK'
                    ]
                ])->response()->setStatusCode(201);
    }

    public function logout($id)
    {
        $user = User::where('id',$id)->first();

        if( count($user) <=0 ){
            return Response::json( [
                'status' => [
                    "code" => 400,
                    "description" => 'Bad Request!'
                ]
                ], 400 );
            }else{
                Auth::logout();

                return Response::json( [
                    'status' => [
                        "code" => 200,
                        "description" => 'OK'
                    ]
                ], 200 );
        }
    }
}
