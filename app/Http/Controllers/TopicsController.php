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

	foreach($topics as &$tmp){
		$tmp->state = (int)$tmp->state;
		$tmp->priority = (int)$tmp ->priority;
		$tmp->isTask = $tmp->isTask == 0 ? false : true;
	}

                return response()->json(['error'=>false,'message' => 'topic list', 'topicList' => $topics->values()], 200);
    }

    function add(Request $request, $idSubject){
	$TOTALMAXTOPICSTATE = 3;
        $user = User::where('api_token', $request->header('Api-Token'))->first();

        $topic = Topic::create([
	    'idSubject' => $idSubject,
            'name' => $request->name,
            'isTask' => $request->isTask == false ? 0 : 1,
            'state' => $request->state,
            'priority' => $request->priority
        ]);


        $topic->state = (int)$topic->state;
        $topic->priority = (int)$topic->priority;
        $topic->isTask = $topic->isTask == 0 ? false : true;

	$topics = Topic::all()->where('idSubject', $idSubject)->sum('state');
	$totalSubjectTopics = Topic::all()->where('idSubject',$idSubject)->count();
        $newPercent = (int)(($topics * 100) / ($totalSubjectTopics * $TOTALMAXTOPICSTATE));



        return response()->json(['error'=>'false','message' => 'Topic created sucefully', 'percent'=>$newPercent, 'topic' => $topic], 201);
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
        if($user->id == $subject->idUser) {
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
