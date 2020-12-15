<?php //Class for immitate playing match and keep information of it

namespace App\League;

class Match {
	
		private $team1, $team2;
		private $score1, $score2;
		
		public function __construct(Team $team1, Team $team2) {
				$this->team1 = $team1;
				$this->team2 = $team2;
				
				$this->playGame();
				
		}
		
		//Getting result of game
		public function getResult():array {
			return Array('host' => $this->score1, "guest" => $this->score2);
		}
		
		
		//Getting Team name by status
		public function getTeamStatus(Team $team):String {

			if (!in_array($team, Array($this->team1, $this->team2)))
				return false;
			
			return ($team == $this->team1) ? 'host' : 'guest';
		}
		
		
		public function getStatisticByTeam(Team $team):int {

			if (!in_array($team, Array($this->team1, $this->team2)))
				return false;
			
			return ($team == $this->team1) ? 1 : 2;
		}
		
		
		public function getHost(): Team { //Host team
			return $this->team1;
		}
		
		
		
		public function getGuest(): Team { //Guest team
			return $this->team2;
		}
		
		
		public function getScore1(): int { //Host team scores
			return $this->score1;
		}
		
		
		public function getScore2(): int { //Guest team scores
			return $this->score2;
		}
		
		
		private function playGame():array { //immitate playing game
			$t1 = $this->team1;
			$t2 = $this->team2;
			
			//getting power of teams
			$diff = round(($t1->calcPower()-$t2->calcPower())/200);
			
			 if ($diff > 0 ) {
					$diff_one = $diff;
					$diff_two = 0;
			 }
			 
			 else {
					$diff_one = 0;
					$diff_two = -$diff;
			 }
			
			//power effect to game result
			$this->score1 = rand(0,4) + rand(0, $diff_one);
			$this->score2 = rand(0,4) + rand(0, $diff_two);
			
			$t1->addGame($this);
			$t2->addGame($this);
			
			return $this->getResult();
		}
}
	
	
?>