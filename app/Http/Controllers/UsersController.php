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
        if ($request->isJson()) {
            //Recogemos los datos
            $data = $request->json()->all();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'api_token' => Str::random(60)

            ]);

            return response()->json($user, 201);
        }

        return response()->json(['ERROR' => 'Unauthorized'],401,[]);
    }

    function getToken(Request $request)
    {
        if ($request->isJson()) {
            try {
                $data = $request->json()->all();
                $user = User::where('email', $data['email'])->first();

                if ($user && Hash::check($data['password'], $user->password)) {
                    return response()->json($user, 200);
                } else {
                    return response()->json(['error' => 'No content'], 406);
                }
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content'], 406);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401, []);
        }
    }

    public function delete(Request $request, $id)
    {
        if ($request->isJson()) {
            User::findOrFail($id)->delete();
            return response('Deleted Successfully', 200);
        }

        return response()->json(['ERROR' => 'Unauthorized'],401,[]);
    }
}
