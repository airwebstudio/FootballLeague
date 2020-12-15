<?php
//Main league singleton Class

namespace App\League;


class League {
	
	private static $lg;
	
	
	private $matches = Array();//played matches	
	private $tours = Array();//played tours
	private $tours_list = Array(); //tours list plan
	
	private $teams = Array(); //teams
	private $teams_names = Array(); //tems names
	
	
	//STATIC FUNCTIONS:	
	
	public static function startNew(array $teams):League {   //Start new League
		
			return self::$lg = new League($teams);	
		
	}	
	
	public static function getCurrent() {   //Get current League from Session
		return self::$lg = request()->session()->get('lg');				
	}
	
	//Clear Team least
	public static function clear($all = false) {
		
		if (!$all)
			return self::$lg = new League(self::$lg->get_teams_names());
		else {
			
			request()->session()->forget('lg');
			throw new \Exception('New League', 503);
			
		}
		 
	}
		
	
	public static function getToursList(array $teams):array { //Return TimeTable tours plans
		
		$matches = Array();
		
		$teams_count = sizeof($teams);
		
		$ind = 0;
		
		for($i1 = 1; $i1 <= $teams_count; $i1++)	
		for($i2 = 1; $i2 <= $teams_count; $i2++) {
				if ($i1 != $i2) {
					$ind ++;
					
					$match = Array('host' => $i1, 'guest' => $i2);
					$matches[$ind] = $match;
					
				}
		}
		
		do {
			$used_teams = Array();				
			$tour  = Array();				
			
			while ($match_arr = self::popMatch($used_teams, $matches)) {
					foreach ($match_arr as $_m) {
						$used_teams[] = $_m;
					}
					
					$tour[] = $match_arr;
					
			}
			
			$tours[] = $tour;
			
		}
		while (!empty($used_teams));
		
		
		
		foreach ($tours as &$tour) {
			foreach ($tour as &$match) {
				$match['host'] = $teams[$match['host']-1];
				$match['guest'] = $teams[$match['guest']-1];
			}
				
		}
		
		return $tours;
			
	}
	
		
	private static function PopMatch(array $used_teams = Array(), array &$matches) { //Getting match by params	
		
		$_matches = $matches1 = $matches;
		
		foreach($used_teams as $_used_team) {
			foreach ($_matches as $m_ind => $_m) {
				
				foreach ($_m as $_m_team) {
						if ($_used_team == $_m_team) {
							unset($matches1[$m_ind]);
							break;
						}
				}						
				
			}
		}
		
		foreach ($matches1 as $m_index => $_match) {
				unset($matches[$m_index]);
				return $_match;
		}
		
		return false;
	}
	
	
	//constructor
	
	private function __construct(array $teams) { 
		
		if (sizeof($teams) < 4) {
			throw new \Exception('No teams or less than 4');	
		}
	
		//$this->clear();
		
		$i = 0;	
		foreach ($teams as $_team) {
				$i++;				
				$this->addTeam(new Team($_team, $i));
		}
		
		$this->tours_list = League::getToursList($this->teams);		
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
	
	//Clear Team list
	public function _clear($all = false):League {
		unset($this->teams);
		//$this->teams = Array();
		
		
		if ($all) {
			unset($this->teams_names);
			$this->teams_names = array();
			
			unset($this->tours_list);
			$this->tours_list = array();			
		}
		
		
		$i = 0;
		foreach($this->teams_names as $t) {
			$i++;
			$this->teams[] = new Team($t, $i);			
		}			
		
		//unset($this->tours);
		$this->tours = array();		
				
		//$this->saveCurrent();
		
		return $this;
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
		
		return $results;
		
	}
	
	
	
	public function get($ind):Team {   //Get Team by index
			return $this->teams[$ind-1];
	}
	
	
	public function getLeagueTable():array { //get Teams
			return $this->teams;
	}	
	
	
	public function saveCurrent() {  //Save current League to Session
		return request()->session()->put('lg', $this);		
	}
	
	
	
	public function get_teams_list():array { //get all teams
			return $this->teams;
	}
	
	
	
	public function nextWeek() { //Play next Tour
		
		
		$t = new Tour($this->tours_list[sizeof($this->tours)]);
		
		$matches = $t->getMatches();
		$this->tours[] = $t;
		$this->sortTable();
		
		$this->saveCurrent();
		
		
		return $t;
		
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
	
	
	
	public function sortTable():bool { //Sort League table by achivments of every Team
		
			//if (sizeof($this->teams) == 0) return false;
			
			usort($this->teams, function($t1, $t2){
				
				
				if ($t1->get_scores() > $t2->get_scores()) 
					return false;
				
				elseif ($t2->get_scores() > $t1->get_scores())
					return true;
				else {
					
						//if both teams has the same scores
						
						//Personal matches
						$matches = $t1->getMatches()[$t2->getIndex()] ?? false;
						
						if ($matches !== false) {
							$host_scores1 = 0;
							$host_scores2 = 0;
							
							$guest_scores1 = 0;
							$guest_scores2 = 0;
							
							
							foreach ($matches as $match) {
								
								
								$res = $match->getResult();
								
								if ($match->getTeamStatus($t1) == 'host') {
									
									
									$host_scores1 += $res['host'];
									$host_scores2 += $res['guest'];
								
								}
								else {
									$guest_scores1 += $res['guest'];
									$guest_scores2 += $res['host'];
								}
							}
							
							$scores1 = $host_scores1 + $guest_scores1;
							$scores2 = $host_scores2 + $guest_scores2;
							
							
							//echo $t1->get_name(). ' - '. $t2->get_name() ;
							if ($scores1 > $scores2) {
								
								return false;
								
							}
							
							elseif ($scores2 > $scores1) {
								return true;
								
							}
							 
							 if ($host_scores1 > $guest_scores1) {
								 return false;
							 }
							 
							 elseif($host_scores1 < $guest_scores1) {
									return true;
							 }	
						}
						
							
						//if both teams has the same scores and played in draw.						 
						//goals differance
						return ($t1->get_goals_scored() - $t1->get_goals_conceded() > $t2->get_goals_scored() - $t1->get_goals_conceded());
																 
						 
					}
				
			});
			
			$this->rate_all();
			
			return true;
	}
	
	
	private function rate_all() {   //Calc Chances to for each team
		
		if ($this->get_tours_count() == 0)
			return false;
		
		
		$rates = [];
		
		$i = 0;
		
		$base_rate = 100 / $this->get_teams_count();								
		$tours_sizeof = self::get_tours_count();
		
		
		foreach ($this->teams as $t) {
			
				if ($t->get_matches_count() == 0) {
					$rates[] = $base_rate;
				}
				
				$tour_i = pow($t->get_matches_count()/$tours_sizeof, 2);
				
				
				$rates[] = $base_rate + ($t->calcPower()*$tour_i) ;
				$i++;
		}
		
		
		//TO %		
		$min = min($rates);
		$max = max($rates);
		
		if ($min <= 0) {
				foreach ($rates as &$r) {
						$r += -$min + 1;
				}
		}
		
		$sum = array_sum($rates);
		foreach ($rates as &$r) {
				$r = round(($r / $sum)*100, 2);
				
		}
		
		$i = 0;
		foreach ($this->teams as $t) {			
				
			$t->set_rate($rates[$i]);
			$i++;
		}
		
	}
	
}
	
?>