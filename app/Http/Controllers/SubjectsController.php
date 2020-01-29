<?php

namespace App\Http\Controllers;

use App\Subject;
use App\User;
use App\Topic;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
        function list(Request $request)
    {
        $user = User::where('api_token', $request->header('Api-Token'))->first();
        $subject = Subject::all()->where('idUser',$user->id);

	foreach($subject as &$tmp){
		$tmp->topicList = Topic::all()->where('idSubject', $tmp->id)->values();
	}

        return response()->json(['error'=>'false','count'=>$subject->count(),'subjects' => $subject->values()], 200);

    }

    function add(Request $request){
        $user = User::where('api_token', $request->header('Api-Token'))->first();

//dd($request->estate_priority);

        $subject = Subject::create([
            'idUser' => $user->id,
            'subject_name' => $request->subject_name,
            'estate_priority' => $request->estate_priority
        ]);

        return response()->json(['error'=>'false','message' => 'subject created sucefully', 'subject' => $subject], 201);
	}




    function delete(Request $request,$id)
    {
        $user = User::where('api_token', $request->header('Api-Token'))->first();
        try {
            $subject = Subject::findOrFail($id);
        }catch (ModelNotFoundException $e){
            return response()->json(['error' => 'true', 'message' => 'the subject does not exist','subject'=>null], 202);
        }
        if($user->id ==$subject->idUser) {
            $subject->delete();

            return response()->json(['error' => 'false', 'message' => 'Subject deleted Successfully','subject'=>$subject], 200);
        }
        else
            return response()->json(['error' => 'false', 'message' => 'not your subject', 'deleted subject' => null], 200);



    }

    public function update($id, Request $request)
    {
        $user = User::where('api_token', $request->header('Api-Token'))->first();
        $subject = Subject::findOrFail($id);
        if($user->id ==$subject->idUser) {
            Subject::findOrFail($id)->update([
                'subject_name' => $request->subject_name,
                'estate_priority' => $request->estate_priority,
            ]);

            return response()->json(['error' => 'false', 'message' => 'subject updated'], 200);
        }
        else
            return response()->json(['error' => 'false', 'message' => 'not your subject', 'subject' => null], 200);
    }
}
