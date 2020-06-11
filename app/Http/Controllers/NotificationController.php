<?php

namespace App\Http\Controllers;

use App\Subject;
use App\User;
use App\Topic;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

	function getToday(Request $request){

        $TOTALMAXTOPICSTATE = 3;

        $user = User::where('api_token', $request->header('Api-Token'))->first();
        $subject = Subject::all()->where('idUser',$user->id);

        //Esto recoge todos los temas de una asignatura dado ese id y calcula el porcentaje de la asignatura
        foreach($subject as &$tmp){
                $topics = Topic::all()->where('idSubject', $tmp->id)->sum('state');
                $tmp->exam_date = date('d-m-Y',strtotime($tmp->exam_date));
                if($topics == 0){
                        $tmp->percent = 0;
                }
                else{
                        $totalSubjectTopics = Topic::all()->where('idSubject',$tmp->id)->count();
                        $tmp->percent = (int)(($topics * 100) / ($totalSubjectTopics * $TOTALMAXTOPICSTATE));
                }
        }

        return response()->json(['error'=>false,'count'=>$subject->count(),'subjects' => $subject->values()], 200);

    }

    function add(Request $request){
        $user = User::where('api_token', $request->header('Api-Token'))->first();

	//dd(date('Y-m-d',strtotime($request->date)));  Esta línea convierte el formato dd-MM-yyyy en formato yyyy-MM-dd que es aceptado por la base de datos

        $subject = Subject::create([
            'idUser' => $user->id,
            'subject_name' => $request->subject_name,
            'exam_date' => date('Y-m-d',strtotime($request->date)),
	    'color' => $request->color,
            'iconId' => $request->iconId
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
	    $topicsDeleted =  Topic::all()->where('idSubject', $subject->id);


        foreach($topicsDeleted as &$tmp){
                $tmp->state = (int)$tmp->state;
                $tmp->priority = (int)$tmp ->priority;
                $tmp->isTask = $tmp->isTask == 0 ? false : true;
        }

            $subject->delete();

            return response()->json(['error' => 'false', 'message' => 'Subject deleted Successfully','deletedSubject'=>$subject, 'topicsDeleted'=>$topicsDeleted->values()], 200);
        }
        else{
            return response()->json(['error' => 'false', 'message' => 'not your subject', 'deleted subject' => null], 200);
	}
    }

    public function update($id, Request $request)
    {
	$oldSubject = null;
        $user = User::where('api_token', $request->header('Api-Token'))->first();
        $subject = Subject::findOrFail($id);
        if($user->id ==$subject->idUser) {
	    $oldSubject = $subject;
            Subject::findOrFail($id)->update([
                'subject_name' => $request->subject_name,
                'exam_date' => date('Y-m-d',strtotime($request->date)),
                'color' => $request->color,
                'iconId' => $request->iconId
            ]);

            return response()->json(['error' => 'false', 'message' => 'subject updated', 'oldSubject'=>$oldSubject, 'updatedSubject'=>$subject], 200);
        }
        else{
            return response()->json(['error' => 'false', 'message' => 'not your subject', 'subject' => null], 200);
	}
    }
}
