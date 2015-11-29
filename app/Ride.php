<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Ride extends Model
{
    protected $fillable = ['name', 'api_name'];
    
    /**
	 * Relationship to Park
	 */
	public function park()
	{
		return $this->belongsTo('App\Park');
	}
	
	/**
	 * Relationship to Wait Time
	 */
	public function waittimes()
	{
		return $this->hasMany('App\WaitTime');
	}
    
    /**
	 * Fetches the current ride information
	 *
	 * @param string $park (the theme park to fetch rides for)
	 *
	 * @return array
	 */
	public function fetchRideData($park)
	{
		if (!empty($park))
		{
			// The URL for the API
			$url = 'http://dlwait.zingled.com/' . $park;
			
			// Return the array
			return $this->fetchJSON($url);
		}
	}
	
	/**
	 * Uses cURL to get data from a url
	 *
	 * @param string $url
	 *
	 * @return array
	 */
	protected function fetchJSON($url)
    {
	    if (!empty($url))
	    {
		    // initiate curl
		    $ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL,$url);
			$result=curl_exec($ch);
			curl_close($ch);
			
			// convert the json to an array	
			return json_decode($result);
		}
    }
    
    /**
	 * Get the current wait time for the ride
	 *
	 * @return int
	 */
	public function wait()
	{
		// Get the latest entry for wait time
		$waitTime = $this->waittimes()->orderBy('datetime', 'desc')->first();
		
		if ($waitTime->created_at->format('Y-m-d H') == Carbon::now()->format('Y-m-d H'))
		{
			if (!empty($waitTime->wait))
			{
				return $waitTime->wait;
			}
		}
	}
	
	/**
	 * Get whether or not a ride is open
	 *
	 * @return boolean
	 */
	public function open()
	{
		// Get the latest entry for wait time
		$waitTime = $this->waittimes()->orderBy('datetime', 'desc')->first();
		
		if ($waitTime->created_at->format('Y-m-d H') == Carbon::now()->format('Y-m-d H'))
		{
			if (!empty($waitTime->status))
			{
				if ($waitTime->status == 'Open')
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
			}
		}
		else 
		{
			return FALSE;
		}
	}
	
	/**
	 * Average wait time today
	 *
	 * @param array $daterange
	 *
	 * @return int
	 */
	public function avgWait($daterange = '')
	{
		if (empty($daterange))
		{
			$daterange[0] = Carbon::today();
			$daterange[1] = Carbon::tomorrow();
		}
		
		$waitTimes = $this->waittimes()
						   ->where('datetime', '>=', $daterange[0])
						   ->where('datetime', '<=', $daterange[1])
						   ->get();
						   
	    $totalWait = 0;
	    $countWait = 0;
	    	    
	    foreach ($waitTimes as $wait)
	    {
		    if (!empty($wait->wait))
		    {
			    $totalWait += $wait->wait;
			    $countWait++;
		    }
	    }
	    
	    if ($countWait != 0)
	    {
		    return intval($totalWait / $countWait);
	    }
	    
	}
}
