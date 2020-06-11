<?php

namespace App\Http\Controllers;

use App\User;
use App\VerifyMail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use \Illuminate\Mail\Message;
use DateTime;

class UsersController extends Controller
{
    function index(Request $request)
    {
       	$user = User::all();
        foreach($user as &$tmp){
		$tmp->verify_at == null ? $tmp->verified = false : $tmp->verified = true;
        }
        return response()->json($user, 200);
    }

    function createUser(Request $request){;
	$user = User::where('email', $request->email)->first();

        if(is_null($user)){
        	$user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
	        'api_token' => Str::random(60),
		'verify_at' => null
        	]);

	        $mesage = ' we are excited to see you join in our team!';
        	$code = Str::random(8);

	        Mail::send('emails.register', ['user' => $user->name, 'mesage'=>$mesage, 'code'=>$code], function ($m) use ($user) {
	            $m->from('mail.focusapp@gmail.com', 'Focus Team');
	            $m->to($user->email, $user->name)->subject('Welcome to focus!');
	        });


		verifyMail::create([
		'idUser' => $user->id,
		'verification_code' => $code
		]);

                return response()->json(['message'=>'created','user'=> $user], 201);
            }
            else{
                return response()->json(['message'=>'duplicate email','user'=> null], 201);
            }
	}


    function getUser(Request $request)
    {
            try {

                $user = User::where('email', $request->email)->first();
                if(is_null($user))
                    throw new ModelNotFoundException();

                if ($user && Hash::check($request->password, $user->password)) {
		    $user->verify_at == null ? $user->verified = false : $user->verified = true;
                    return response()->json(['error'=>'false','message'=>'logged','user'=> $user], 200);
                } else {
                    return response()->json(['error'=>'true','message'=>'wrong login','api_token'=> ""], 401);
                }
            } catch (ModelNotFoundException $e) {
                return response()->json(['error'=>'true','message'=>'email not in database','api_token'=> ""], 402);
            }
    }

    function delete(Request $request)
    {
        User::where('api_token', $request->header('Api-Token'))->first()->delete();
            return response('Deleted Successfully', 200);
    }




    function verifyUser(Request $request)
    {
        $user = User::where('api_token', $request->header('Api-Token'))->first();
	$verify = VerifyMail::where('idUser', $user->id)->first();

	if($verify->verification_code == $request->verification_code){
		$user->verify_at = strftime(date('Y-m-d H:i:s'));
		$user->save();
		$user->verified = true;
		return response()->json(['error'=>false,'message'=>'User verified','user'=> $user], 200);
	}else{
		$user->verified = false;
		return response()->json(['error'=>true,'message'=>'User not verified, wrong code', 'user'=> $user], 200);

	}
    }


    function resendMail(Request $request){;
        $user = User::where('api_token', $request->header('Api-Token'))->first();

	VerifyMail::where('idUser', $user->id)->update([
        'verification_code' => Str::random(8)
        ]);
	$verify = VerifyMail::where('idUser', $user->id)->first();

        $mesage = ' we are excited to see you join in our team!';

        Mail::send('emails.register', ['user' => $user->name, 'mesage'=>$mesage, 'code'=>$verify->verification_code], function ($m) use ($user) {
           $m->from('mail.focusapp@gmail.com', 'Focus Team');
           $m->to($user->email, $user->name)->subject('Welcome to focus!');
        });

        return response()->json(['error'=>false, 'message'=>'Email send with a new code'], 201);
        }


    function rescoverPasswordMail(Request $request){;
        $user = User::where('api_token', $request->header('Api-Token'))->first();

        VerifyMail::where('idUser', $user->id)->update([
        'verification_code' => Str::random(8)
        ]);
        $verify = VerifyMail::where('idUser', $user->id)->first();

        $mesage = ' have problem triying to logging in the app?, dont care :D';

        Mail::send('emails.recoverPass', ['user' => $user->name, 'mesage'=>$mesage, 'code'=>$verify->verification_code], function ($m) use ($user) {
           $m->from('mail.focusapp@gmail.com', 'Focus: Reset password');
           $m->to($user->email, $user->name)->subject('Focus: Recover password');
        });

        return response()->json(['error'=>false, 'message'=>'Email send with a recover password code'], 201);
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
