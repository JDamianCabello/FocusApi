<?php

namespace App\Http\Controllers;

use App\Subject;
use App\User;
use http\Env\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    function listSubject(Request $request)
    {

        $user = User::where('api_token', $request->header('Api-Token'))->first();
            //Esto usa Eloquent para sacar los datos
            $subject = Subject::where('idUser',$user['id']);
            return response()->json($subject, 200);
    }

    function createSubject(Request $request){
            $user = User::where('api_token', $request->header('Api-Token'))->first();
        $subject = Subject::create([
            'idUser' => $user['id'],
            'subject_name' => $request->subject_name,
            'estate_priority' => $request->estate_priority,
        ]);

        return response()->json(['message' => 'subject created sucefully', 'subject' => $subject], 201);
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
        Subject::findOrFail($id)->delete();
        return response('Subject eleted Successfully', 200);
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
