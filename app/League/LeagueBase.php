<?php
namespace App\League;

class LeagueBase {
	
	protected $matches = Array();//played matches	
	protected $tours = Array();//played tours
	protected $tours_list = Array(); //tours list plan
	
	protected $teams = Array(); //teams
	protected $teams_names = Array(); //teams names
	
	

	//constructor
	
	public function __construct(array $teams) { 
		
		if (sizeof($teams) < 4) {
			throw new \Exception('No teams or less than 4');	
		}
	
		//$this->clear();
		
		$i = 0;	
		foreach ($teams as $_team) {
				$i++;				
				$this->addTeam(new Team($_team, $i));
		}
		
		$this->tours_list = TimeTableGen::getToursList($this->teams);		
		$this->teams_names = $teams;
		
		$this->saveCurrent();
		
		
	}
	
	//METHODS:
	
	public function get_teams_count():int { //getting sizeof of teams
		return $this->teams ? sizeof($this->teams) : 0;
	}
	
	//all tours in League
	public function get_tours_count():int {
			return sizeof($this->teams )*2 - 2;
	}
	
	//add Team
	public function addTeam(Team $team) {			
			$this->teams[] = $team;
	}
	
	
	public function get_teams_names() {
		return $this->teams_names;
	}
	
	
	public function getTourTable() { //League Table
		
		$data = [];
		//preparing League Table
		foreach ($this->getLeagueTable() as $item) {
			$data[] = Array('name' => $item->get_name(), 
							'games' => $item->get_matches_count(), 
							'scores' => $item->get_scores(),
							'wins' => $item->get_wins(),
							'draws' => $item->get_draws(),
							'looses' => $item->get_looses(),
							'goals' => ($item->get_goals_scored() - $item->get_goals_conceded())
							
			);
			
		}
		
		return $data;
	}
	
	public function getPredictsTable() {
			$predicts = [];
			
			//Predicts
			foreach ($this->getLeagueTable() as $item) {
				$predicts[] = Array('name' => $item->get_name(), 'chance' => $item->get_rate());
			}
			
			//sort Predicts by Chances		
			usort($predicts, function($p1, $p2){
				return ($p1['chance'] < $p2['chance']);
			});
			
			return $predicts;
	}
	
	public function getRestTourList():array { //Next games
		
			$arr = [];
			for ($i = sizeof($this->tours); $i < sizeof($this->tours_list); $i++) {
				
				foreach ($this->tours_list[$i] as $t) {
					$arr[$i+1][] = Array('host' => $t['host']->get_name(), 
										 'guest' => $t['guest']->get_name() );
				}
			}
			
			return $arr;
	}
	
	public function getLeagueStatus() {  //Status of league now
			
		if ($this->ToursPlayed() == 0)
			$status = 'new';
		
		elseif ($this->ToursToPlay() == 0) {
			$status = 'finished';	
		}
		else {
			$status = 'playing';
		}
		
		return $status;
	}
	
	
	public function getViewResults() { //get everything for view
		
		$results = Array();
		
		
		$results['tour_played'] = $this->ToursPlayed();
		
		$results['tour_to_play'] = $this->ToursToPlay();		
		
		$results['league_table'] = $this->getTourTable();		
		$results['next_games'] = $this->getRestTourList();
		$results['predicts_table'] = $this->getPredictsTable();
		
		$results['league_status'] = $this->getLeagueStatus();
		
		
		
		
		$tour = $this->getLastTour();
		
		if (isset($tour)) {
			$results['games_table'] = $tour->getMatches();
		}
		
		return $results;
		
	}
	
	
	public function get($ind):Team {   //Get Team by index
			return $this->teams[$ind-1];
	}
	
	
	public function getLeagueTable():array { //get Teams
			return $this->teams;
	}	
	
	
	public function saveCurrent() {  //Save current League to Session
		return \MyStorage::save('lg', $this);		
	}
	
	
	
	public function get_teams_list():array { //get all teams
			return $this->teams;
	}
	
	
	public function getlastTour() { //Return a last Played Tour or NULL if League have't start yet		
		
		if (!isset($this->tours[sizeof($this->tours)-1]))
			throw new \Exception('New League', 503);
	
		return  $this->tours[sizeof($this->tours)-1];
		
	}
	
	public function ToursPlayed():int { //sizeof of played games
			return sizeof($this->tours);
	}
	
	
	public function ToursToPlay():int {	 //sizeof of games have to play	
			return (sizeof($this->tours_list) - sizeof($this->tours)-1);
	}
	
	
}