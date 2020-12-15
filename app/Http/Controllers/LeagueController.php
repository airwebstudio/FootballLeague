<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;


//add MY NAMESPACE
use \App\League\League;


class LeagueController {
	
	
	//up-table
	public function upload_teams(Request $request) {
		
		if (!$request->has('teams'))
			return response()->json('Error', 503);
		
		$teams = $request->only('teams')['teams'];
		
		if (!League::startNew($teams)) 
			return response()->json('Validate Error', 53);
		
		
		return response()->json('OK', 200);
	}
	
	
	//get-table
	public function get_table(Request $request) {
		
		
		try {
			//getting current League
			$lg = League::getCurrent();	
			
			//what is next action from front-end
			$next = $request->get('next');
			
			
			//case input action command
			switch ($next) {
		
				case 'week': case 'all':
							
							$tour = $lg->nextWeek();
							//recursive repeat untill the League is over					
							if (($next == 'all') && (!empty($t))) {
									return $this->get_table($request);
							}
							
							break;
						
				case 'reset': case  'reset_all': 
							$teams = Array();
							if (isset($lg) && ($next == 'reset')) {				
								$teams = $lg->get_teams_list();
							}			
							
							$lg = League::startNew($teams);
							
							break;
				
				
				default:  $t = $lg->lastTour();
						
			}
		
			
		}
		
		catch(Exception $e) {
			//if Error! No teams
			return response()->json(array('error' => $e->getMessage()), 503);
			
		}
		
		
		//Return results
		$results = $lg->getViewResults();
		
		if (isset($tour)) {
			$results = $tour->getMatches();
		}
			
			
		//return json
		return response()->json($results, 200);		
		
	}
}
