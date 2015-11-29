<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use View;
use DB;

use App\Park;
use App\Ride;
use App\WaitTime;

class RideController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
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
        $ride = Ride::find($id);
        $waittimes = new \stdClass();
        $waittimes->all = $ride->waittimes()->orderBy('created_at')->get();
        
        // Get averages by day of the week 
        // EVENTUALLY TURN THIS QUERY INTO SQL IF POSSIBLE
        $waittimes->daysOfWeek = new \stdClass();
        $waittimes->daysOfWeek->Sunday = $this->getWaitAverageDay($ride, 1);        
        $waittimes->daysOfWeek->Monday = $this->getWaitAverageDay($ride, 2);        
        $waittimes->daysOfWeek->Tuesday = $this->getWaitAverageDay($ride, 3);        
        $waittimes->daysOfWeek->Wednesday = $this->getWaitAverageDay($ride, 4);        
        $waittimes->daysOfWeek->Thursday = $this->getWaitAverageDay($ride, 5);        
        $waittimes->daysOfWeek->Friday = $this->getWaitAverageDay($ride, 6);        
        $waittimes->daysOfWeek->Saturday = $this->getWaitAverageDay($ride, 7);  
        
        // Get averages by Month
        $waittimes->months = new \stdClass();
        
        for ($i = 1; $i < 13; $i++)
        {
	        $waittimes->months->{$this->getMonthByInt($i)} = $this->getWaitAverageMonth($ride, $i);
        }
        
        // By hour
        $waittimes->hours = $this->waitTimesByHour($ride);
        
        $data['ride'] = $ride;
        $data['waittimes'] = $waittimes;
        return View::make('rides.single', $data);
    }
    
    
    public function getWaitAverageMonth($ride, $month)
    {
		return $ride->waittimes()->where(DB::raw('MONTH(created_at)'), $month)->where('status', '!=', 'complete')->where('wait', '!=', '')->avg('wait');
    }
    
    
    public function getWaitAverageDay($ride, $day)
    {
	    return $ride->waittimes()->where(DB::raw('DAYOFWEEK(created_at)'), $day)->where('status', '!=', 'complete')->where('wait', '!=', '')->avg('wait');
    }
    
    public function getWaitAverageHour($ride, $hour)
    {
	    // Convert the hour from West Coast to UTC
	    $hour = Carbon::today(new \DateTimeZone('America/Los_Angeles'))->addHours($hour - 2)->timezone('UTC')->format('H');
	    return $ride->waittimes()->where(DB::raw('HOUR(created_at)'), $hour)->where('status', '!=', 'complete')->where('wait', '!=', '')->avg('wait');
    }
    
    
    public function getMonthByInt($int)
    {
	    $months = array(
		    1 => 'Jan',
		    2 => 'Feb',
		    3 => 'Mar',
		    4 => 'Apr',
		    5 => 'May',
		    6 => 'Jun',
		    7 => 'Jul',
		    8 => 'Aug',
		    9 => 'Sep',
		    10 => 'Oct',
		    11 => 'Nov',
		    12 => 'Dec'
	    );
	    
	    return $months[$int];
    }
    
    
    public function waitTimesByHour($ride)
    {
	    $hours = array();
	    
	    for ($i = 0; $i < 24; $i++)
	    {
		    $item = new \stdClass();
		    $item->hour = $i;
		    $item->wait = $this->getWaitAverageHour($ride, $i);
		    $hours[$i] = $item;
	    }
	    
	    return $hours;
    }
    
    private function sortHours($a, $b)
    {
	    if ($a->wait == $b->wait) return 0;
	    return ($a->wait > $b->wait) ? 1 : -1;
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
    
    /**
	 * Fetch New Ride Data
	 */
	public function fetchRideData()
	{
		// Fetch Parks
		$parks = Park::all();
		
		// Get a ride object so we can query through it
		$rideQuery = new Ride();
		
		// Get the current time as a carbon object
		$now = Carbon::now();
				
		// Loop through each park and fetch those rides
		foreach ($parks as $park)
		{			
			// Is the park even open?
			$parkHours = $park->fetchHours($park->name, $now);			
			$parkOpen = Carbon::createFromFormat('Y-m-d g:i a', $now->format('Y-m-d ') . $parkHours[0]);
			$parkClose = Carbon::createFromFormat('Y-m-d g:i a', $now->format('Y-m-d ') . $parkHours[1]);
									
			if ($now->between($parkOpen, $parkClose))
			{
				
				// Fetch the ride data from the API
				$rideData = $rideQuery->fetchRideData($park->api_name);
								
				// Loop through each ride
				foreach ($rideData as $data)
				{
					// Does the ride already exist here? If not, create it
					if (count(Ride::where('api_name', $data->name)->get()) == 0)
					{
						// Create the new ride
						$newRide = new Ride();
						$newRide->api_name = $data->name;
						
						// Attach it to the park
						$park->rides()->save($newRide);
					}
					
					// New wait time object so we can query and create the new object
					$wait = new WaitTime();
					$ride = Ride::where('api_name', $data->name)->get()->first();
													
					// Set the time for the entry (nearest quarter hour)
					$minutes = $wait->roundTime($now->format('i'));				
					$roundedTime = Carbon::createFromFormat(
						'Y-m-d H:i:s', 
						$now->format('Y') . '-' . $now->format('m') . '-' . $now->format('d') . ' ' . $now->format('H') . ':' . $minutes . ':00', 
						'America/Los_Angeles'
					);
					
					// Does this entry already exist for this ride?
					if (count(WaitTime::where('ride_id', $ride->id)->where('datetime', $roundedTime)->get()) == 0)
					{
						if (!empty($data->waitTime))
						{
							// Convert the string from the API to an integer
							$waitTime = $wait->calcWaitTime($data->waitTime);
							
							// Create the new wait time entry
							$wait->wait = (!empty($waitTime)) ? $waitTime : 0;
							$wait->status = $wait->calcStatus($data->waitTime);
							$wait->datetime = $roundedTime;
							
							$ride->waittimes()->save($wait);
						}
					}
				}
			}
		}
		
		// echo the ride data the api sent over
		return \Response::json($rideData);
	}
	
	/**
	 * Fetches Park Hours
	 * Used to test connectivity to disney site
	 */
	public function fetchParkHours($parkName = 'Disneyland')
	{
		$now = Carbon::now();
		$park = new Park();
				
		return \Response::json($park->fetchHours($parkName, $now));
	}
	 
}
