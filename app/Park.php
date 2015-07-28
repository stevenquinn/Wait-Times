<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Park extends Model
{
    protected $fillable = ['name', 'api_name'];
    
    /**
	 * Relationship to rides
	 */
	public function rides()
	{
		return $this->hasMany('App\Ride');
	}
	
	/**
	 * Gets the park hours for a specific day
	 *
	 * @param string $park
	 * @param obj $date
	 *
	 * @return array
	 */
	public function fetchHours($park, $date)
	{	
		// Set the park value to look for
        switch ($park)
        {
	        case 'Disneyland':
		        $park = 'Disneyland Park';
		        break;
		    
		    case 'California Adventure':
			    $park = 'Disney California Adventure Park';
			    break;
			    
			default:
				$park = '';
        }
        
        if (!empty($park))
        {
			// Use cURL to fetch the page we'll scrape
			$url = 'https://disneyland.disney.go.com/au/calendar/daily/?day=' . $date->format('Ymd');
			$ch = curl_init();  // Initialising cURL
	        curl_setopt($ch, CURLOPT_URL, $url);    // Setting cURL's URL option with the $url variable passed into the function
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Setting cURL's option to return the webpage data
	        $data = curl_exec($ch); // Executing the cURL request and assigning the returned data to the $data variable
	        curl_close($ch);
	        
	        // Parse the HTML
	        libxml_use_internal_errors(true);
	        $dom = new \DOMDocument();
	        $dom->loadHTML($data);
	        
	        // Get the park hours table
	        $parkHours = $dom->getElementById('parkHours');
	        
	        // Get to the tbody element
	        foreach ($parkHours->childNodes as $tr)
	        {
				$foundPark = FALSE;
				
				if ($tr->hasChildNodes())
				{
			        // get the row element
			        foreach ($tr->childNodes as $td)
			        {
				        // Loop through the columns
				        if ($td->nodeValue == $park)
				        {
					        $foundPark = TRUE;
				        }
				        else
				        {
					        // Is the the row for the park (and the second td?)
					        if ($foundPark)
					        {
						        $timeString = $td->nodeValue;
						        
						        // Convert this to an array
						        $time = explode(' to ', $timeString);
						        
						        if (count($time) == 2)
						        {
							        // If it's midnight, fall back one minute in order to have the time math work
							        if ($time[1] == '12:00 a.m.')
							        {
									    $time[1] = '11:59 p.m.';    
							        }
							        							        
								    return array($time[0], $time[1]);    
						        }
					        }
				        }				       
			        }
		        }
	        }
        }
       
	}
	
}
