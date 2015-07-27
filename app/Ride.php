<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    protected $fillable = ['name', 'api_name'];
    
    /**
	 * Relationship to Park
	 */
	public function rides()
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
}
