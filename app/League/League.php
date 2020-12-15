<?php
//Main league singleton Class

namespace App\League;


class League {
	
	private static $lg;
	
	private $matches = Array();
	private $tours = Array();
	private $tours_list = Array();
	private $teams = Array();
	
	
	
	//TEAMS SECTION
	
	private $teams = Array();	
	
	
	private static $teams_count;		
	private static $matches = Array();

	//Return TimeTable
	public static function getTours(int $teams_count):array {
		
		self::$teams_count = $teams_count;
			
		$ind = 0;
		
		for($i1 = 1; $i1 <=$teams_count; $i1++)	
		for($i2 = 1; $i2 <=$teams_count; $i2++) {
				if ($i1 != $i2) {
					$ind ++;
					
					$match = Array('host' => $i1, 'guest' => $i2);
					self::$matches[$ind] = $match;
					
				}
		}
		
		do {
			$used_teams = Array();				
			$tour  = Array();				
			
			while ($match_arr = self::popMatch($used_teams)) {
					foreach ($match_arr as $_m) {
						$used_teams[] = $_m;
					}
					
					$tour[] = $match_arr;
					
			}
			
			$tours[] = $tour;
			
		}
		while (!empty($used_teams));
		
		return $tours;
			
	}
	
	//Getting match by params		
	private static function PopMatch(array $used_teams = Array()) {
		
		$_matches = $matches = self::$matches;
		
		foreach($used_teams as $_used_team) {
			foreach ($_matches as $m_ind => $_m) {
				
				foreach ($_m as $_m_team) {
						if ($_used_team == $_m_team) {
							unset($matches[$m_ind]);
							break;
						}
				}						
				
			}
		}
		
		foreach ($matches as $m_index => $_match) {
				unset(self::$matches[$m_index]);
				return $_match;
		}
		
		return false;
	}

	//getting sizeof of teams
	public function get_teams_count():int {
		return $this->$teams ? sizeof($this->$teams) : 0;
	}
	
	//all tours in League
	public function get_tours_sizeof():int {
			return sizeof($this->$teams )*2 - 2;
	}
	
	
	//add Team
	public function addTeam(Team $team) {
			
			$this->$teams[] = $team;
	}
	
	
	//Clear Team least
	public function clear():void{
		$this->$teams  = array();
		$this->saveCurrent();
		
	}
	
	
	public function getTourTable() {
		
		$data = [];
		//preparing League Table
		foreach ($this->getTable() as $item) {
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
			foreach ($this->getTable() as $item) {
				$predicts[] = Array('name' => $item->get_name(), 'chance' => $item->get_rate());
			}
			
			//sort Predicts by Chances		
			usort($predicts, function($p1, $p2){
				return ($p1['chance'] < $p2['chance']);
			});
			
			return $predicts;
	}
	
	public function getLeagueStatus() {
			//Status of league now
		if ($this->ToursPlayed() == 0)
			$results['league_status'] = 'new';
		
		elseif ($this->ToursToPlay() == 0) {
			$results['league_status'] = 'finished';	
		}
		else {
			$results['league_status'] = 'playing';
		}
	}
	
	
	public function getViewResults() {
		
		$results = Array();
		
		$results['tour_played'] = $this->ToursPlayed();
		$results['tour_to_play'] = $this->ToursToPlay();
		
		
		$results['league_table'] = $this->getLeagueTable();		
		$results['next_games'] = $this->getRestTourList();
		$results['predicts_table'] = $this->getPredicts();
		
		return $results;
		
	}
	
	
	//Get Team by index
	public function get($ind):Team {
			return $this->$teams[$ind-1];
	}
	
	//Calc Chances to for each team
	private function rate_all() {
		
		if ($this->get_tours_count() == 0)
			return false;
		
		
		$rates = [];
		
		$i = 0;
		
		$base_rate = 100 / $this->get_teams_count();								
		$tours_sizeof = self::get_tours_count();
		
		
		foreach ($this->$teams as $t) {
			
				if ($t->get_matches_count() == 0) {
					$rates[] = $base_rate;
				}
					
			
				/*if ($i == 0) {
						$lead_team = $t;
				}*/				
				//$lead_scores = $lead_team->get_scores();				
				//$scores_rate = (((($t->get_scores())/($t->get_matches_sizeof())) - ($lead_scores/$lead_team->get_matches_sizeof())) * pow($t->get_matches_sizeof()/self::get_tours_sizeof(), 3)*120);
				
				
				
				$tour_i = pow($t->get_matches_sizeof()/$tours_sizeof, 2);
				
				
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
		foreach ($this->$teams as $t) {			
				
			$t->set_rate($rates[$i]);
			$i++;
		}
		
	}
	
	
	//Sort League table by achivments of every Team
	
	public function sortTable():bool {
		
			if (sizeof($this->$teams) == 0) return false;
			
			usort($this->$teams, function($t1, $t2){
				
				
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
	
	//get Teams
	public function getTable():array {
			return $this->$teams;
	}
	
	
	
	
	//Start new League
	public static function startNew(array $teams):League {
		//if (!isset(self::$lg)) {
			self::$lg = new League($teams);	
			self::$lg::saveCurrent();
		//}
		
		return self::$lg;
	}
	
	
	//Get current League from Session
	public static function getCurrent() {
		self::$lg = request()->session()->get('lg');		
			
		return self::$lg ?? null;
	}
	
	
	//Save current League to Session
	public static function saveCurrent() {
		return request()->session()->put('lg', self::$lg);
		
	}
		
	
	
	private function __construct(array $teams) {
		
		/*if (isset($_SESSION['league_games']))
		$this->games = $tours;*/
	
		if (sizeof($teams) < 4) {
			throw new Exception('No teams or less than 4');	
		}
	
		$this->clear();
		
		$i == 0;	
		foreach ($teams as $_team) {
			
				$i++;				
				$this->addTeam(new Team($_team, $i));
		}
		
		//Teams::saveTeamsToSession();

		$_tours_list_schema = self::getTours(sizeof($this->teams));
		
		$tours_list = [];
		
		foreach ($_tours_list_schema as $t) {
				$tour_list[] = Array('host' => $teams[$t['host']], 'guest' => $teams[$t['guest']]);
		}
		
		$this->tours_list = $tours_list;
		
		
		$this->teams = $teams;
	
		
	}
	//get all teams
	public function get_teams_list():array {
			return $this->teams;
	}
	
	
	//Play next Tour
	public function nextWeek() {
		
		$t = new Tour($this->tours_list[sizeof($this->tours)]);
		
		$matches = $t->getMatches() ?? false;
		
		if (!$matches) {
			return null;
		}
		
		$this->matches = array_merge_recursive($this->matches, $matches);
		$this->tours[] = $t;
		
		return $t;
		
	}
	
	
	//Return a last Played Tour or NULL if League have't start yet
	public static function lastTour() {
		
		$res =self::$lg->tours[sizeof($this->tours)-1] ?? null;
		
		if (isset($res))
		self::$lg->sortTable();
		
		
		return $res;	
	}
	
	
	//sizeof of played games
	public function ToursPlayed():int {
			return sizeof($this->tours);
	}
	
	//sizeof of games have to play
	public function ToursToPlay():int {
		
			return (sizeof($this->tours_list) - sizeof($this->tours)-1);
	}
	
	
	//Next games
	public function getRestTourList():array {
		
			$arr = [];
			for ($i = sizeof($this->tours); $i < sizeof($this->tours_list); $i++) {
				
				foreach ($this->tours_list[$i] as $t) {
					$arr[$i+1][] = Array('host' => $t['host']->get_name(), 
										 'guest' => $t['guest']->get_name() );
				}
			}
			
			return $arr;
	}
	
}
	
?>