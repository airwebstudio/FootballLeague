<?php //Class of Each Team in League. Params and Statistic


namespace App\League;

use Illuminate\Support\Str;

class Team {
		
	private $name;
	private $index;
	
	private $rate = 0;
	private $chance = 100;
	
	private $scores = 0;
	
	private $wins = 0;
	private $looses = 0;
	private $draws = 0;
	
	
	private $goals_scored = 0;
	private $goals_conceded = 0;
	
	private $no_looses_in_row = 0;
	private $looses_in_row = 0;
	
	private $guest_wins = 0;
	private $home_looses = 0;
	
	private $matches;
	
	private $matches_count = 0;
	
	private $wins_in_row = 0;
	
		
	
	public function __construct(String $name, int $ind) {
		$this->name = $name;
		
		$this->index = (int)$ind;
		
		
	}
	
	
	//get_ {any of parameter}
	public function __call($method, $parameters) {
			if (Str::startsWith($method, 'get_')) {
				$var = substr($method, 4);
				
				if (isset($this->$var))
					return $this->$var;
				else
					return false;
			}
	}
	
	
	//set chance
	public function set_rate($rate) {
		$this->rate = $rate;
		
	}

	
	
	//Calculate Team power by
	public function calcPower() {
		
		$rate = 0;
		
		if ($this->matches_count == 0) return 0;
		
		$rate += $this->scores * 40;		
		$rate += ($this->wins - $this->matches_count) * 10; // wins to all matches
		$rate += ($this->wins + $this->draws - $this->matches_count) * 5; // no looses to all matches
		$rate += ($this->looses - $this->matches_count) * -10; // looses to all matches		
		$rate += ($this->home_looses) * (-10); // home looses matches		
		$rate += ($this->wins - $this->looses)*15; // wins to looses		
		$rate += ($this->wins + $this->draws - $this->looses)*5; // no looses to looses		
		$rate += $this->wins_in_row * 12; // wins in row
		$rate += $this->no_looses_in_row * 10; // no looses in row
		$rate += $this->guest_wins * 10; // guest wins in row
		
		$rate += ($this->goals_scored - $this->goals_conceded)*20;
		
		$rate = $rate / $this->matches_count;
		
		return round($rate, 4);
		
		
	}
	

	//Add new game to the Team statistic
	public function addGame(Match $match) {
			
			$t1 = $match->getHost();
			$t2 = $match->getGuest();
			
			
			if (!$status = $match->getTeamStatus($this))
				return false;
			
			
			//Is this Team guest or host
			if ($status == 'guest') {
				$my_score = $match->getScore2();
				$opp_score = $match->getScore1();
				
				$me = $t2;
				$opp = $t1;
				
			}
			else {
				$my_score = $match->getScore1();
				$opp_score = $match->getScore2();
				
				
				$me = $t1;
				$opp = $t2;
			}
				
				//Save info to Table
				
				$this->goals_scored += $my_score;  
				$this->goals_conceded += $opp_score;
				
				if ($my_score > $opp_score) {
						$this->wins++;
						$this->scores += 3;
						$this->no_looses_in_row += 1;
						$this->wins_in_row += 1;
						
						$this->looses_in_row = 0;
						
						if ($status == 'guest') {
							$this->guest_wins++;
						}
				}
				elseif ($my_score < $opp_score) {
						$this->looses++;
						$this->no_looses_in_row += 1;
						$this->looses_in_row = 0;
						$this->wins_in_row = 0;
				}
				else {
						$this->draws++;
						$this->scores += 1;
						
						$this->looses_in_row += 1;
						$this->no_looses_in_row = 0;
						$this->wins_in_row = 0;
						
						
						if ($status == 'home') {
							$this->home_looses++;
						}
				}	
				
			//save match
			$this->matches[$opp->getIndex()][] = $match;			
			$this->matches_count++;			
			
			
	}
	
	
	//get Index
	public function getIndex(): int {
			return $this->index;
	}
	
		
	
	
}

?>