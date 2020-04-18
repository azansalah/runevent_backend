<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Event;
use App\Http\Models\Package;
use App\Http\Models\Registration;
use App\Http\Models\Runner;
use Faker\Factory;

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
        if($package) {
            $runner = Runner::where('card_no', $this->request->input('cardNo'))->first();
            if($runner) {
                $dataUpdate = [
                    't_name' => $this->request->input('tName'),
                    'f_name' => $this->request->input('fName'),
                    'l_name' => $this->request->input('lName'),
                    'telephone' => $this->request->input('telephone'),
                    'email' => $this->request->input('email'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                Runner::where('card_no', $this->request->input('cardNo'))->update($dataUpdate);
            }else {
                $runnerId = uniqid();
                Runner::create([
                    'id' =>  $runnerId,
                    'card_no' => $this->request->input('cardNo'),
                    't_name' => $this->request->input('tName'),
                    'f_name' => $this->request->input('fName'),
                    'l_name' => $this->request->input('lName'),
                    'telephone' => $this->request->input('telephone'),
                    'email' => $this->request->input('email'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $runner = Runner::where('card_no', $this->request->input('cardNo'))->first();
            }

            $checkAlReady = Registration::where('package_id', $id)->where('runner_id', $runner->id)->first();
            if($checkAlReady) {
                return responder()->error()->respond(400);
            }
            $countRegis = Registration::where('package_id', $id)->get()->count();
            $canRegis = false;
            if($package->is_limit) {
                if($countRegis < $package->limit_count) {
                    $canRegis = true;
                }
            }else {
                $canRegis = true;
            }

            if($canRegis) {
                $registerId = uniqid();
                Registration::create([
                    'id' => $registerId,
                    'runner_id' => $runner->id,
                    'package_id' => $id,
                    'register_date' => date('Y-m-d H:i:s')
                ]);

                return responder()->success()->respond(200);
            }else {
                return responder()->error()->respond(400);
            }
        }
    }

    public function fakeData()
    {
        $faker = Factory::create();
        
        for ($i = 0; $i < 235; $i++) {
            try {
                $runnerId = uniqid();
                Runner::create([
                    'id' =>  $runnerId,
                    'card_no' => $faker->numberBetween(0000000000000, 9999999999999),
                    't_name' => $faker->title,
                    'f_name' => $faker->firstName,
                    'l_name' => $faker->lastName,
                    'telephone' => $faker->numberBetween(0000000000, 9999999999),
                    'email' => $faker->email,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $registerId = uniqid();
                Registration::create([
                    'id' => $registerId,
                    'runner_id' => $runnerId,
                    'package_id' => '5e9aa50b8394d',
                    'register_date' => date('Y-m-d H:i:s')
                ]);
            }catch(Exeption $e) {
                continue;
            }
        }

        return 'success';
        
    }

}
