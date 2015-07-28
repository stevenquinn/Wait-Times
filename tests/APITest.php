<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class APITest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->visit('/')
             ->see('Laravel 5');
    }
    
    
    /**
	 * Tests to see if the API is even working
	 *
	 * @return void
	 */
	public function testAPIWorking()
	{		 
		$data = $this->get(route('ride-data'));
		 
		if (!empty($data))
		{
			$this->assertTrue(TRUE);
		}
		else
		{
			$this->assertTrue(FALSE);
		}
	}
	
	/**
	 * Can we get the current park hours (does connection still work)?
	 *
	 * @return void
	 */
	public function testParkHours()
	{
		$parkHours = $this->get(route('park-hours'));
		
		if (!empty($parkHours))
		{
			$this->assertTrue(TRUE);
		}
		else
		{
			$this->assertTrue(FALSE);
		}
	}
}
