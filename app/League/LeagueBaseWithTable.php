<?php
namespace App\League;

class LeagueBaseWithTable extends LeagueBase {
	
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