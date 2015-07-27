<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WaitTime extends Model
{
    protected $fillable = ['datetime', 'wait', 'status'];
    
    /**
	 * Relationship to Ride
	 */
	public function ride()
	{
		return $this->belongsTo('App\Ride');
	}
	
	
	/**
	 * Convert the wait time from the api (with minutes) to just the integer
	 *
	 * @param string $waittime
	 *
	 * @return int
	 */
	public function calcWaitTime($waittime)
	{
		// Check if anything's passed
		if (!empty($waittime))
		{
			// Strip away the "minutes"
			$waittime = explode(' minutes', $waittime);
			
			// Return only the int
			if (!empty($waittime[0]))
			{
				return $waittime[0];
			}
		}
	}
	
	/**
	 * Return a 'status' for the ride
	 *
	 * @param string $waittime
	 *
	 * @return string
	 */
	public function calcStatus($waittime)
	{
		if ($waittime == 'Closed')
		{
			return 'Closed';
		}
		else
		{
			return 'Open';
		}
	}
	
	/**
	 * Rounds the current time down to the nearest quarter hour
	 *
	 * @param unix timestamp $time
	 *
	 * @return int
	 */
	public function roundTime($minutes)
	{		
		$rounded = $minutes - ($minutes % 15);
		
		// if it's a single digit #, prepend with a 0
		if (strlen($rounded) == 1)
		{
			$rounded = 0 . $rounded;
		}
		
	    return $rounded;
	}
}
