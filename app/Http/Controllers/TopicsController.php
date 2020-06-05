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
            $topic = Topic::findOrFail($id);
        }catch (ModelNotFoundException $e){
            return response()->json(['error' => 'true', 'message' => 'the topic does not exist','topic'=>null], 202);
        }
	$subject = Subject::where('id',$topic->idSubject)->first();
        if($user->id == $subject->id) {
            $topic->delete();

            return response()->json(['error' => 'false', 'message' => 'Topic deleted Successfully','topic'=>$topic], 200);
        }
        else
            return response()->json(['error' => 'false', 'message' => 'Not your topic', 'deleted topic' => null], 200);



    }

    public function update($id, Request $request)
    {
        $user = User::where('api_token', $request->header('Api-Token'))->first();
        $topic = Topic::findOrFail($id);
	$subject = Subject::where('id',$topic->idSubject)->first();

	$oldTopic = $topic;
        if($user->id == $subject->idUser) {
            Subject::findOrFail($id)->update([
                'name' => $request->name,
                'isTask' => $request->isTask,
                'state' => $request->state,
                'priority' => $request->priority,
                'notes' => $request->notes
            ]);

            return response()->json(['error' => 'false', 'message' => 'topic updated', 'oldTopic'=>$oldTopic, 'updatedTopic'=>$topic], 200);
        }
        else
            return response()->json(['error' => 'false', 'message' => 'Not your topic', 'topic' => null], 200);
    }
}
