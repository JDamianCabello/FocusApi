<?php

namespace App\Http\Controllers;

use App\Subject;
use App\User;
use App\Topic;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{

   function list(Request $request){

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
			$tmp->percent = ($topics * 100) / ($totalSubjectTopics * $TOTALMAXTOPICSTATE);
		}
	};

        return response()->json(['error'=>'false','count'=>$subject->count(),'subjects' => $subject->values()], 200);

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
