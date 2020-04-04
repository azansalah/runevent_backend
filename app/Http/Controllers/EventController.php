<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Models\Event;


class EventController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    public function getEventList()
    {
        $events = Event::get();

        $result = [];
        foreach($events as $event){
            array_push($result, [
                'id' => $event->id,
                'name' => $event->name,
                'eventLocation' => $event->event_location,
                'date' => $event->date
            ]);
        }
        $data = [
            'result' => $result,
        ];

        return responder()->success($data)->respond(200);
    }

    public function getEvent($id = null)
    {
        if($id) {
            $event = Event::find($id);
            if($event) {
                $result = [
                    'id' => $event->id,
                    'name' => $event->name,
                    'eventLocation' => $event->event_location,
                    'date' => $event->date
                ];
    
                $data = [
                    'result' => $result,
                ];
                return responder()->success($data)->respond(200);
            }else {
                return responder()->error()->respond(404);
            }
        } else {
            return responder()->error()->respond(400);
        }
    }



    public function addEvent()
    {
        $validator = Validator::make($this->request->all(), [
            'date' => 'date_format:Y-m-d H:i:s'
        ]);

        if ($validator->fails()) {
            $messages = $validator->messages();
            return responder()->error()->data([
                'validate' => $messages,
            ])->respond(400);
        }

        $id = uniqid();
        Event::create([
            'id' => $id,
            'name' => $this->request->input('name'),
            'event_location' => $this->request->input('eventLocation'),
            'date' => $this->request->input('date')
        ]);

        $event = Event::find($id);
        if($event) {
            return $this->getEvent($id);
        }else {
            return responder()->error()->respond(400);
        }
    }

    public function editEvent($id = null)
    {
        if($id) {

            $validator = Validator::make($this->request->all(), [
                'date' => 'date_format:Y-m-d H:i:s'
            ]);
    
            if ($validator->fails()) {
                $messages = $validator->messages();
                return responder()->error()->data([
                    'validate' => $messages,
                ])->respond(400);
            }

            $event = Event::find($id);
            if($event) {
                $event->update([
                    'name' => $this->request->input('name'),
                    'event_location' => $this->request->input('eventLocation'),
                    'date' => $this->request->input('date')
                ]);

                return $this->getEvent($id);
            }else {
                return responder()->error()->respond(404);
            }
        } else {
            return responder()->error()->respond(400);
        }
    }

    public function deleteEvent()
    {
        if($this->request->has('eventList')){
            if(is_array($this->request->input('eventList')) && $this->request->input('eventList') !== []){
                $eventList = $this->request->input('eventList');
                Event::whereIn('id', $eventList)->delete();

                return responder()->success()->respond(200);
            }else {
                return responder()->error()->respond(400);
            }
        }else {
            return responder()->error()->respond(400);
        }
        
    }

}
