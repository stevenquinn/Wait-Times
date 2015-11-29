<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Carbon\Carbon;

use App\Park;

class ParkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $parks = Park::all();
        $data['parks'] = $parks;
        
        return View::make('parks.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
	    // Get Park name and hours
	    $now = Carbon::now();
        $park = Park::find($id);
        $parkHours = $park->fetchHours($park->name, $now);
        
        // Set the closing time back to midnight
        if ($parkHours[1] == '11:59 p.m.')
        {
	        $parkHours[1] = '12:00 p.m.';
        }
        
        // Get ride information
        $rides = $park->rides;
        
        // Get the max wait time and distrubution
        $max = 0;
        $waitDist = new \StdClass();
        $waitDist->low = 0;
        $waitDist->med = 0;
        $waitDist->high = 0;
        $waitDist->closed = 0;
         
        foreach ($rides as $ride)
        {
	        if ($max < $ride->wait())
	        {
		        $max = $ride->wait();
	        }
	        
	        if ($ride->wait() == 0) 
	        {
		        $waitDist->closed++;
	        }
	        elseif ($ride->wait() < 20)
	        {
		        $waitDist->low++;
	        }
	        elseif ($ride->wait() < 45)
	        {
		        $waitDist->med++;
	        }
	        else
	        {
		        $waitDist->high++;
	        }
        }
                
        $data['park'] = $park;
        $data['parkOpen'] = $parkHours[0];
        $data['parkClose'] = $parkHours[1];
        $data['rides'] = $rides;
        $data['waitMax'] = ($max != 0) ? $max : 1;
        $data['waitDist'] = $waitDist;
        return View::make('parks.single', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
