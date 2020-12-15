<?php
//Main league singleton Class

namespace App\League;


class League {
	
	private $matches = Array();
	private $tours = Array();
	private $tours_list = Array();
	private $teams = Array();
	
	private static $lg;
	
	
	//Start new League
	public static function startNew(array $teams):League {
		//if (!isset(self::$lg)) {
			self::$lg = new League($teams);	
			self::saveCurrent();
		//}
		
		return self::$lg;
	}
	
	
	//Get current League from Session
	public static function getCurrent() {
		self::$lg = request()->session()->get('lg');		
		Teams::loadTeamsFromSession();
		
		
		return self::$lg ?? null;
	}
	
	
	//Save current League to Session
	public static function saveCurrent() {
		return request()->session()->put('lg', self::$lg);
		
	}
		
	
	
	private function __construct(array $teams) {
		
		/*if (isset($_SESSION['league_games']))
		$this->games = $tours;*/
	
		Teams::clear();
	
		foreach ($teams as $_team) {
				
				Teams::addTeam(new Team($_team, Teams::get_count()+1));
		}
		
		Teams::saveTeamsToSession();

		$this->tours_list = Match_list::getTours(Teams::get_count());
		$this->teams = $teams;
	
		
	}
	//get all teams
	public function get_teams_list():array {
			return $this->teams;
	}
	
	
	//Play next Tour
	public function nextWeek() {
		
		$t = new Tour($this->tours_list[count($this->tours)]);
		
		$matches = $t->getMatches() ?? false;
		
		if (!$matches) {
			return null;
		}
		
		$this->matches = array_merge_recursive($this->matches, $matches);
		$this->tours[] = $t;
		
		return $t;
		
	}
	
	
	//Return a last Played Tour or NULL if League have't start yet
	public function lastTour() {
		
		$res = $this->tours[count($this->tours)-1] ?? null;
		
		if (isset($res))
		Teams::sortTable();
		
		
		return $res;	
	}
	
	
	//count of played games
	public function ToursPlayed():int {
			return count($this->tours);
	}
	
	//count of games have to play
	public function ToursToPlay():int {
		
			return (count($this->tours_list) - count($this->tours)-1);
	}
	
	
	//Next games
	public function getRestTourList():array {
		
			$arr = [];
			for ($i = count($this->tours); $i < count($this->tours_list); $i++) {
				
				foreach ($this->tours_list[$i] as $t) {
					$arr[$i+1][] = Array('owner' => Teams::get($t['owner'])->get_name(), 'guest' => Teams::get($t['guest'])->get_name() );
				}
			}
			
			return $arr;
	}
	
}
	
?>