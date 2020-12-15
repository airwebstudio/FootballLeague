<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;


//add League namepace
use \App\League\League;


class LeagueController {
	
	
	//up-table
	public function upload_teams(Request $request) {
		
		if (!$request->has('teams'))
			return response()->json('Error', 503);
		
		$teams = $request->only('teams')['teams'];
		
		if (!League::startNew($teams)) 
			return response()->json('Validate Error', 503);		
		
		return response()->json('OK', 200);
	}
	
	
	
	//get-table
	public function get_table(Request $request) {
		
		try {
			
			$next = $request->get('next');			
			$lg = League::getCurrent();
			
			if (!isset($lg))
				throw new \Exception('New League', 503);
			
			//case input action command
			switch ($next) {	
				
					case 'reset': case 'reset_all': 
						$lg = League::clear($next == 'reset_all');					
						break;
			
					case 'all': case 'week':
							
							if ($lg->toursToPlay() > 0) {
								$tour = $lg->nextWeek();
											
								if ($next == 'all')  { //recursive repeat untill the League is over	
										return $this->get_table($request);
								}
							}
							else {
								$tour = $lg->getLastTour();
							}
							
							break;				
				default:		
					
						
			}
			
			
		} catch(\Throwable $e) { //if Error! No teams			
			
			$c = ($e->getCode() == 503) ? 503: 500;			
			return response()->json(array('error' => $e->getMessage().$e->getLine()), $c);
			
		}
		
		
		//Return results
		$results = $lg->getViewResults();
		
		if (isset($tour)) {
			$results['games_table'] = $tour->getMatches();
		}
			
			
		//return json
		return response()->json($results, 200);		
		
	}
}
