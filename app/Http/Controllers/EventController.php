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
                'created_at' => $event->created_ad,
                'updated_at' => $event->update_at,
                'deleted_at' => $event->deleted_at,
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

    public function dashBoard()
    {
        $query1 = "SELECT
                        COUNT(a.runner_id) AS count_regis,
                        b.name AS package_name,
                        b.price AS package_price,
                        c.name AS event_name
                    FROM
                        registrations a
                    LEFT JOIN packages b ON a.package_id = b.id
                    LEFT JOIN events c ON b.event_id = c.id
                    GROUP BY b.id
                    ORDER BY count_regis DESC
                    LIMIT 10
        ";

        $rows1 = DB::select($query1);

        $packageStat = [];
        foreach($rows1 as $row) {
            array_push($packageStat, [
                'packageName' => $row->package_name,
                'eventName' => $row->event_name,
                'packagePrice' => $row->package_price,
                'countRegis' => $row->count_regis,
            ]);
        }

        $query2 = "SELECT
                        COUNT(a.runner_id) AS count_regis,
                        c.name AS event_name,
                        c.location as location
                    FROM
                        registrations a
                    LEFT JOIN packages b ON a.package_id = b.id
                    LEFT JOIN events c ON b.event_id = c.id
                    GROUP BY c.id
                    ORDER BY count_regis DESC
                    LIMIT 10
        ";

        $rows2 = DB::select($query2);
        
        $eventStat = [];
        foreach($rows2 as $row) {
            array_push($eventStat, [
                'eventName' => $row->event_name,
                'location' => $row->location,
                'countRegis' => $row->count_regis,
            ]);
        }

        $query3 = "SELECT
                        COUNT(id) as count_regis,
                        CAST(register_date AS DATE) AS regis_date
                    FROM
                        registrations
                    GROUP BY regis_date
                    ORDER BY regis_date DESC
                    LIMIT 30
        ";

        $rows3 = DB::select($query3);

        $dataX = [];
        $dataY = [];
        foreach($rows3 as $row) {
            array_push($dataX, $row->regis_date);
            array_push($dataY, $row->count_regis);
        }
        $result = [
            'packageStat' => $packageStat,
            'eventStat' => $eventStat,
            'chart' => [
                'dataX' => $dataX,
                'dataY' => $dataY
            ]
        ];

        $data = [
            'result' => $result
        ];
        return responder()->success($data)->respond(200);
    }

}
