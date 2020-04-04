<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getEventList()
    {
        $query = "SELECT *
        FROM events
        ";

        $events = DB::connection('mysql')->select($query);
        return $events;
    }
}
