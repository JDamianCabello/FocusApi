<?php

namespace App\Http\Controllers;

use App\Subject;
use App\User;
use App\Topic;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TopicsController extends Controller
{
        function list(Request $request, $idSubject)
    {
        $user = User::where('api_token', $request->header('Api-Token'))->first();
        $topics = Topic::all()->where('idSubject',$idSubject);

        return response()->json(['error'=>'false','count'=>$topics->count(),'topicList' => $topics->values()], 200);

    }

    function add(Request $request, $idSubject){
        $user = User::where('api_token', $request->header('Api-Token'))->first();

        $topic = Topic::create([
            'idSubject' => $idSubject,
            'name' => $request->name,
            'isTask' => $request->isTask,
	    'state' => $request->state,
            'priority' => $request->priority,
   	    'notes' => $request->notes
        ]);

        return response()->json(['error'=>'false','message' => 'topic created sucefully', 'topic' => $topic], 201);
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
