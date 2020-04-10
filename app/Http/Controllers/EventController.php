<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Models\Event;
use App\Http\Models\Package;


class EventController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    public function getEventList()
    {
        $events = Event::orderBy('updated_at', 'DESC')->get();

        $result = [];
        foreach($events as $event){
            $packages = Package::where('event_id',$event->id)->get();
            $packageList = [];
            foreach($packages as $package){
                array_push($packageList,[
                    'id' => $package->id,
                    'name' => $package->name,
                    'date' => $package->date,
                    'time' => $package->time,
                    'price' => $package->price,
                    'isLimit' => $package->is_limit,
                    'limitCount' => $package->limit_count,
                ]);
            }
            array_push($result, [
                'id' => $event->id,
                'name' => $event->name,
                'location' => $event->location,
                'date' => $event->date,
                'packages' => $packageList
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
                $packages = Package::where('event_id',$event->id)->get();

                $packageList = [];
                foreach($packages as $package){
                    array_push($packageList,[
                        'id' => $package->id,
                        'name' => $package->name,
                        'date' => $package->date,
                        'time' => $package->time,
                        'price' => $package->price,
                        'isLimit' => $package->is_limit,
                        'limitCount' => $package->limit_count,
                    ]);
                }
                $result = [
                    'id' => $event->id,
                    'name' => $event->name,
                    'location' => $event->location,
                    'date' => $event->date,
                    'packages' => $packageList
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
            'name' => 'required',
            'location' => 'required',
            'packages.*.name' => 'required',
            'packages.*.date' => 'required|date_format:Y-m-d',
            'packages.*.time' => 'required|date_format:H:i',
            'packages.*.price' => 'required|numeric',
            'packages.*.isLimit' => 'required|boolean',
            'packages.*.limitCount' => 'nullable|numeric',


        ]);

        if ($validator->fails()) {
            $messages = $validator->messages();
            return responder()->error()->data([
                'validate' => $messages,
            ])->respond(400);
        }

        $eventId = uniqid();
        Event::create([
            'id' => $eventId,
            'name' => $this->request->input('name'),
            'location' => $this->request->input('location'), 
        ]);

        $packages = $this->request->input('packages');
        foreach($packages as $package){
            $packageId = uniqid();
            Package::create([
                'id' => $packageId,
                'event_id' => $eventId,
                'name' => $package['name'],
                'date' => $package['date'],
                'time' => $package['time'],
                'price' => $package['price'],
                'is_limit' => $package['isLimit'],
                'limit_count' => $package['limitCount'],
            ]);
        }


        $event = Event::find($eventId);
        if($event) {
            return $this->getEvent($eventId);
        }else {
            return responder()->error()->respond(400);
        }
    }

    public function editEvent($id = null)
    {
        if($id) {

            $validator = Validator::make($this->request->all(), [
                'name' => 'required',
                'location' => 'required',
                'packqges.*.name' => 'required',
                'packqges.*.date' => 'required|date_format:Y-d-m',
                'packqges.*.time' => 'required|date_format:H:i',
                'packqges.*.price' => 'required|numeric',
                'packqges.*.islimit' => 'required|boolean',
                'packqges.*.limitcount' => 'required|numeric',

                
            ]);
    
            if ($validator->fails()) {
                $messages = $validator->messages();
                return responder()->error()->data([
                    'validate' => $messages,
                ])->respond(400);
            }

            $event = Event::find($id);
            if($event) {

                $packages = $this->request->input('packages');
                foreach($packages as $package){
                    $packageList = [];
                    if($package['id'] != null){
                        $updateData = [
                            'id' => $package['id'],
                            'event_id' => $id,
                            'name' => $package['name'],
                            'date' => $package['date'],
                            'time' => $package['time'],
                            'price' => $package['price'],
                            'is_limit' => $package['isLimit'],
                            'limit_count' => $package['limitCount']
                        ];
                        Package::where('id', $package['id'])->update($updateData);
                        array_push($packageList, $package['id']);
                    }else{
                        $packageId = uniqid();
                        Package::create([
                            'id' => $packageId,
                            'event_id' => $id,
                            'name' => $package['name'],
                            'date' => $package['date'],
                            'time' => $package['time'],
                            'price' => $package['price'],
                            'is_limit' => $package['isLimit'],
                            'limit_count' => $package['limitCount']
                        ]);
                    }

                    if($packageList != []){
                        Package::whereNotIn('id', $packageList)->delete();
                    }
                }

                $event->update([
                    'name' => $this->request->input('name'),
                    'location' => $this->request->input('location')
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
                Package::WhereIn('event_id', $eventList)->delete();

                return responder()->success()->respond(200);
            }else {
                return responder()->error()->respond(400);
            }
        }else {
            return responder()->error()->respond(400);
        }
        
    }

}
