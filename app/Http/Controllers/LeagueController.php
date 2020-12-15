<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;


//add MY NAMESPACE
use \App\League\League;
use \App\League\Teams;


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
		
		//what is next action
		$next = $request->get('next');		
		
		//getting current League
		$lg = League::getCurrent();	
		
		
		//If reset 
		if (($next == 'reset')|| ($next == 'reset_all')) {
			
			$teams = Array();
			if (isset($lg) && ($next == 'reset')) {				
				$teams = $lg->get_teams_list();
			}			
			
			$lg = League::startNew($teams);
					
		}	

		//if Error! No teams
		if ((Teams::get_count() == 0) || (empty($lg))) {
			return response()->json(array('error' => 'No teams created'), 503);
		}
	
		//Check status of request
		if ($next == 'week') {
			
			//Next Week
			$t = $lg->nextWeek();
				
		}
		elseif ($next == 'all') {
			
			//recursive repeat untill the League is over
			$t = $lg->nextWeek();
			
			if (!empty($t)) {
					return $this->get_table($request);
			}
		}
		
		//If no  new tour
		if (!isset($t))
			$t = $lg->lastTour();	
		
		
		//Preparing to outPut
		$data = Array();
		$predicts = Array();
		$games = null;
		$results = Array();
		
		
		
		if (isset($t))
			$_games = $t->getMatches();
		
		//params
		$results['tour_played'] = $lg->ToursPlayed();
		$results['tour_to_play'] = $lg->ToursToPlay();
		
		if (!empty($_games)) {
			
			foreach ($_games as $g) {
					$games[] = Array('team1' => $g->getTeam1()->get_name(), 'team2' => $g->getTeam2()->get_name(), 'score1' => $g->getScore1(),'score2' => $g->getScore2()) ;
			}
			
			$results['games_table'] = $games;
			
		}
		
		//Status of league now
		if ($lg->ToursPlayed() == 0)
			$results['league_status'] = 'new';
		
		elseif ($lg->ToursToPlay() == 0) {
			$results['league_status'] = 'finished';	
		}
		else {
			$results['league_status'] = 'playing';
		}
		
		//preparing League Table
		foreach (Teams::getTable() as $item) {
			$data[] = Array('name' => $item->get_name(), 
							'games' => $item->get_matches_count(), 
							'scores' => $item->get_scores(),
							'wins' => $item->get_wins(),
							'draws' => $item->get_draws(),
							'looses' => $item->get_looses(),
							'goals' => ($item->get_goals_scored() - $item->get_goals_conceded())
							
			);
			
			//Predicts
			$predicts[] = Array('name' => $item->get_name(), 'chance' => $item->get_rate());
		}
		
		//sort Predicts by Chances		
		usort($predicts, function($p1, $p2){
			return ($p1['chance'] < $p2['chance']);
		});
		
		//Return results
		$results['league_table'] = $data;		
		$results['next_games'] = $lg->getRestTourList();
		
		if (!in_array($results['league_status'], Array('first', 'last'))) {
			$results['predicts_table'] = $predicts;
			
		}		
			
		//return json
		return response()->json($results, 200);		
		
	}
}
