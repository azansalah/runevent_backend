<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Event;
use App\Http\Models\Package;
use App\Http\Models\Registration;
use App\Http\Models\Runner;

class EventWebsiteController extends Controller
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
            $packages = Package::where('event_id', $event->id)->get();

            $packageList = [];
            foreach($packages as $package){
                if(date('Y-m-d') <= date('Y-m-d', strtotime($package->date))){
                    $countRegis = Registration::where('package_id', $package->id)->get()->count();
                    $canRegis = false;
                    if($package->is_limit){
                        if($countRegis < $package->limit_count){
                            $canRegis = true;
                        }
                    }else{
                        $canRegis = true;
                    }
                    array_push($packageList, [
                        'id' => $package->id,
                        'name' => $package->name,
                        'date' => $package->date,
                        'time' => $package->time,
                        'price' => $package->price,
                        'isLimit' => $package->is_limit,
                        'limitCount' => $package->limit_count,
                        'countRegis' => $countRegis,
                        'canRegis' => $canRegis
                    ]);
                }
            }
            if($packageList !== []){
                array_push($result, [
                    'id' => $event->id,
                    'name' => $event->name,
                    'location' => $event->location,
                    'packages' => $packageList
                ]);
            }
        }
       
        $data = [
            'result' => $result,
        ];

        return responder()->success($data)->respond(200);

    }

    public function register($id)
    {
        $package = Package::find($id);
        if($package){
            $runner = Runner::where('card_no', $this->request->input('cardNo'))->first();
            if($runner){
                $dataUpdate = [
                    't_name' => $this->request->input('tName'),
                    'f_name' => $this->request->input('fName'),
                    'l_name' => $this->request->input('lName'),
                    'telephone' => $this->request->input('telephone'),
                    'email' => $this->request->input('email'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                Runner::where('card_no', $this->request->input['card_no'])->update($dataUpdate);
            }else {
                $runnerId = uniqid();
                Runner::create([
                    'id' => $runnerId,
                    'card_no' => $this->request->input('cardNo'),
                    't_name' => $this->request->input('tName'),
                    'f_name' => $this->request->input('fName'),
                    'l_name' => $this->request->input('lName'),
                    'telephone' => $this->request->input('telephone'),
                    'email' => $this->request->input('email'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $runner = Runner::where('card_no', $this->request->input['card_no'])->first();
            }

            $checkAlReady = Registration::where('package_id', $id)->where('runner_id', $runner->id)->first();
            if($checkAlReady){
                return responder()->error()->data([
                    'code' => '01',
                    'message' => 'คุณเคิยสมัครแพ็คเกจนี้ไปแล้ว'
                ])->respond(400);
            }
            $countRegis = Registration::where('package_id', $id)->get()->count();
            $canRegis = false;
            if($package->is_limit){
                if($countRegis < $package->limit_count){
                    $canRegis = true;
                }
            }else{
                $canRegis = true;
            }

            if($canRegis){
                $registerId = uniqid();
                Registration::create([
                    'id' => $registerId,
                    'runner_id' => $runner->id,
                    'package_id' => $id,
                    'register_date' => date('Y-m-d H:i:s')
                ]);

                return responder()->success()->respond(200);
            }else{
                return responder()->error()->data([
                    'code' => '02',
                    'message' => 'แพ็คเกจที่คุณสมัคร เต็มแล้ว'
                ])->respond(400);
            }
        }
    }

}
