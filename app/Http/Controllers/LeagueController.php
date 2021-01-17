<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

//add League namepace
use \App\League\LeagueSimulation;


class LeagueController {
	
	
	//up-table
	public function upload_teams(Request $request, LeagueSimulation $league) {
		$league->startNew($request->only('teams')['teams']);			
		return $this->get_table($league);
	}
	
	public function reset(LeagueSimulation $league) {
		$league->clear();		
		return $this->get_table($league);
	}
	
	public function reset_all(LeagueSimulation $league) {
		$league->clear(true);		
		return $this->get_table($league);
	}
	
	
	public function next_week(LeagueSimulation $league) {
		$lg = $league->getCurrent();
		$lg->nextWeek();
		
		return $this->get_table($league);
	}
	
	
	public function play_all(LeagueSimulation $league) {
		$lg = $league->getCurrent();
		$lg->playAll();
		
		return $this->get_table($league);
	}
	
	//get-table
	public function get_table(LeagueSimulation $league) {
	
			$lg = $league->getCurrent();
			return response()->json($lg->getViewResults());
		
		
	}
}
