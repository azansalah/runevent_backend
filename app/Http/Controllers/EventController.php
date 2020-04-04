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
        }else{
            return responder()->error()->respond(400);
        }
    }

    public function editEvent()
    {
    }

}
