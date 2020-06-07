<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use \Illuminate\Mail\Message;

class UsersController extends Controller
{
    function index(Request $request)
    {
            //Esto usa Eloquent para sacar los datos
            $user = User::all();
            return response()->json($user, 200);
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

        $mesage = ' we are excited to see you join in our team!';
        $code = Str::random(50);

        Mail::send('emails.register', ['user' => $user->name, 'mesage'=>$mesage, 'code'=>$code], function ($m) use ($user) {
            $m->from('mail.focusapp@gmail.com', 'Focus Team');

            $m->to($user->email, $user->name)->subject('Welcome to focus!');
        });




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

    function delete(Request $request)
    {
        User::where('api_token', $request->header('Api-Token'))->first()->delete();
            return response('Deleted Successfully', 200);
    }

    public function update($id, Request $request)
    {
            $user = User::findOrFail($id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json($user, 200);
    }
}
