<?php

namespace App\Http\Controllers;

use App\User;
use http\Env\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    function index(Request $request)
    {
        if ($request->isJson()) {
            //Esto usa Eloquent para sacar los datos
            $user = User::all();
            return response()->json($user, 200);
        }

        return response()->json(['ERROR' => 'Unauthorized'],401,[]);
    }

    function createUser(Request $request){
            $user = User::where('email', $request->email)->first();

            if(is_null($user)){
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'api_token' => Str::random(60)
                ]);

                return response()->json(['message'=>'created','user'=> $user], 201);
            }
            else{
                return response()->json(['message'=>'duplicate email','user'=> null], 201);
            }
	}


    function getToken(Request $request)
    {
            try {
                
                $user = User::where('email', $request->email)->first();

                if(is_null($user))
                    throw new ModelNotFoundException();

                if ($user && Hash::check($request->password, $user->password)) {
                    return response()->json(['error'=>'false','message'=>'logged','api_token'=> $user['api_token']], 200);
                } else {
                    return response()->json(['error'=>'true','message'=>'wrong login','api_token'=> ""], 406);
                }
            } catch (ModelNotFoundException $e) {
                return response()->json(['error'=>'true','message'=>'email not in database','api_token'=> null], 406);
            }
    }

    function delete(Request $request, $id)
    {
        if ($request->isJson()) {
            User::findOrFail($id)->delete();
            return response('Deleted Successfully', 200);
        }

        return response()->json(['ERROR' => 'Unauthorized'],401,[]);
    }

    public function update($id, Request $request)
    {
        if ($request->isJson()) {
            $data = $request->json()->all();
            $user = User::findOrFail($id);
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            return response()->json($user, 200);
        }

        return response()->json(['ERROR' => 'Unauthorized'],401,[]);
    }
}
