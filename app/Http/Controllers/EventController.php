<?php

namespace App\Http\Controllers;

use App\Subject;
use App\User;
use App\Topic;
use App\Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class EventController extends Controller
{

	function getDay(Request $request, $date){

	$user = User::where('api_token', $request->header('Api-Token'))->first();
	$events = Event::all()->where('event_date',date('Y-m-d',strtotime($date)))->where('idUser',$user->id);


        foreach($events as &$tmp){
		$tmp->event_date = date('d-m-Y',strtotime($tmp->event_date));
		$tmp->idSubject = $tmp->idSubject == null ? -1 : $tmp->idSubject;
                $tmp->appnotification = $tmp->appnotification == 0 ? false : true;
        }


        return response()->json(['date' => $date,'error'=>false,'events' => $events->values()], 200);

    }

    function getAll(Request $request){

        $user = User::where('api_token', $request->header('Api-Token'))->first();
        $events = Event::all()->where('idUser',$user->id);

        foreach($events as &$tmp){
		$tmp->event_date = date('d-m-Y',strtotime($tmp->event_date));
                $tmp->appnotification = $tmp->appnotification == 0 ? false : true;
		$tmp->idSubject = $tmp->idSubject == null ? -1 : $tmp->idSubject;
        }

        return response()->json(['date' => date('d-m-y'),'error'=>false,'events' => $events->values()], 200);

    }




        function getNotifications(Request $request){

        $user = User::where('api_token', $request->header('Api-Token'))->first();
        $events = Event::all()->where('event_date',date('Y-m-d'))->where('idUser',$user->id);



        foreach($events as &$tmp){
                $tmp->event_date = date('d-m-Y',strtotime($tmp->event_date));
                $tmp->appnotification = $tmp->appnotification == 0 ? false : true;
		$tmp->idSubject = $tmp->idSubject == null ? -1 : $tmp->idSubject;
        }


        return response()->json(['date' => date('d-m-y'),'error'=>false,'events' => $events->values()], 200);

    }



	function add(Request $request){
		$user = User::where('api_token', $request->header('Api-Token'))->first();

                $event = Event::create([
                    	'idUser' => $user->id,
                    	'event_name' => $request->event_name,
                    	'event_resume' => $request->event_resume,
                    	'event_date' =>  date('Y-m-d',strtotime($request->event_date)),
                    	'idSubject' => null,
                    	'event_color' => $request->event_color,
                    	'event_iconId' => $request->event_iconId,
                    	'appnotification' => $request->appnotification == 'true' ? 1 : 0,
                	'event_notes' => $request->event_notes
        	]);


	        $event->event_date = date('d-m-Y',strtotime($event->event_date));
                $event->appnotification = $event->appnotification == 0 ? false : true;
		$event->idSubject = $event->idSubject == null ? -1 : $event->idSubject;
		$event->event_color = (int)$event->event_color;
		$event->event_iconId = (int)$event->event_iconId;



		return response()->json(['error'=>false,'message' => 'Event created sucefully', 'event' => $event], 201);
	}



    function delete(Request $request,$id)
    {

        $user = User::where('api_token', $request->header('Api-Token'))->first();
        try {
            $event = Event::findOrFail($id);
        }catch (ModelNotFoundException $e){
            return response()->json(['error' => true, 'message' => 'the event does not exist','event'=>null], 202);
        }
        if($user->id == $event->idUser) {

            $event->delete();

                $event->event_date = date('d-m-Y',strtotime($event->event_date));
                $event->appnotification = $event->appnotification == 0 ? false : true;
                $event->idSubject = $event->idSubject == null ? -1 : $event->idSubject;
                $event->event_color = (int)$event->event_color;
                $event->event_iconId = (int)$event->event_iconId;



            return response()->json(['error' => false, 'message' => 'Event deleted Successfully','deletedEvent'=>$event], 200);
        }
        else{
            return response()->json(['error' => false, 'message' => 'not your event', 'deleted event' => null], 200);
	}
    }

    function update($id, Request $request)
    {
	$oldEvent = null;
        $user = User::where('api_token', $request->header('Api-Token'))->first();
        $event = Event::findOrFail($id);
        if($user->id == $event->idUser) {
	    $oldEvent = $event;
            Event::findOrFail($id)->update([
                        'event_name' => $request->event_name,
                        'event_resume' => $request->event_resume,
                        'event_date' =>  date('Y-m-d',strtotime($request->event_date)),
                        'idSubject' => null,
                        'event_color' => $request->event_color,
                        'event_iconId' => $request->event_iconId,
                        'appnotification' => $request->appnotification == 'true' ? 1 : 0,
                        'event_notes' => $request->event_notes
                ]);
		$event = Event::findOrFail($id);

                $event->event_date = date('d-m-Y',strtotime($event->event_date));
                $event->appnotification = $event->appnotification == 0 ? false : true;
                $event->idSubject = $event->idSubject == null ? -1 : $event->idSubject;
                $event->event_color = (int)$event->event_color;
                $event->event_iconId = (int)$event->event_iconId;


                $oldEvent->event_date = date('d-m-Y',strtotime($oldEvent->event_date));
                $oldEvent->appnotification = $oldEvent->appnotification == 0 ? false : true;
                $oldEvent->idSubject = $oldEvent->idSubject == null ? -1 : $oldEvent->idSubject;
                $oldEvent->event_color = (int)$oldEvent->event_color;
                $oldEvent->event_iconId = (int)$oldEvent->event_iconId;


            return response()->json(['error' => false, 'message' => 'Event updated', 'oldEvent'=>$oldEvent, 'updatedEvent'=>$event], 200);
        }
        else{
            return response()->json(['error' => false, 'message' => 'Not your event', 'event' => null], 200);
	}
    }
}
