<?php
//static class to manage Teams
namespace App\League;

use Illuminate\Http\Request;

class Teams {
		
	private static $teams = Array();	
	
	//getting count of teams
	public static function get_count():int {
		return self::$teams ? count(self::$teams) : 0;
	}
	
	//all tours in League
	public static function get_tours_count():int {
			return count(self::$teams)*2 - 2;
	}
	
	
	//add Team
	public static function addTeam(Team $team) {
			
			self::$teams[] = $team;
	}
	
	
	//Clear Team least
	public static function clear():void{
		self::$teams = array();
		self::saveTeamsToSession();
		
	}
	
	
	//Get Team by index
	public static function get($ind):Team {
			return self::$teams[$ind-1];
	}
	
	//Calc Chances to for each team
	private static function rate_all() {
		
		if (self::get_tours_count() == 0)
			return false;
		
		
		$rates = [];
		
		$i = 0;
		
		$base_rate = 100 / self::get_count();								
		$tours_count = self::get_tours_count();
		
		
		foreach (self::$teams as $t) {
			
				if ($t->get_matches_count() == 0) {
					$rates[] = $base_rate;
				}
					
			
				/*if ($i == 0) {
						$lead_team = $t;
				}*/
				
				//$lead_scores = $lead_team->get_scores();
				
				//$scores_rate = (((($t->get_scores())/($t->get_matches_count())) - ($lead_scores/$lead_team->get_matches_count())) * pow($t->get_matches_count()/self::get_tours_count(), 3)*120);
				
				
				
				$tour_i = pow($t->get_matches_count()/$tours_count, 2);
				
				
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
		foreach (self::$teams as $t) {
			
				
			$t->set_rate($rates[$i]);
			$i++;
		}
		
	}
	
	
	//Sort League table by achivments of every Team
	
	public static function sortTable():bool {
		
			if (count(self::$teams) == 0) return false;
			
			usort(self::$teams, function($t1, $t2){
				
				
				if ($t1->get_scores() > $t2->get_scores()) 
					return false;
				
				elseif ($t2->get_scores() > $t1->get_scores())
					return true;
				else {
					
						//if both teams has the same scores
						
						//Personal matches
						$matches = $t1->getMatches()[$t2->getIndex()] ?? false;
						
						if ($matches !== false) {
							$owner_scores1 = 0;
							$owner_scores2 = 0;
							
							$guest_scores1 = 0;
							$guest_scores2 = 0;
							
							
							foreach ($matches as $match) {
								
								
								$res = $match->getResult();
								
								if ($match->getTeamStatus($t1) == 'owner') {
									
									
									$owner_scores1 += $res['owner'];
									$owner_scores2 += $res['guest'];
								
								}
								else {
									$guest_scores1 += $res['guest'];
									$guest_scores2 += $res['owner'];
								}
							}
							
							$scores1 = $owner_scores1 + $guest_scores1;
							$scores2 = $owner_scores2 + $guest_scores2;
							
							
							//echo $t1->get_name(). ' - '. $t2->get_name() ;
							if ($scores1 > $scores2) {
								
								return false;
								
							}
							
							elseif ($scores2 > $scores1) {
								return true;
								
							}
							
							
							 
							 if ($owner_scores1 > $guest_scores1) {
								 return false;
							 }
							 
							 elseif($owner_scores1 < $guest_scores1) {
									return true;
							 }	
						}
						
							
						//if both teams has the same scores and played in draw.						 
						//goals differance
						return ($t1->get_goals_scored() - $t1->get_goals_conceded() > $t2->get_goals_scored() - $t1->get_goals_conceded());
																 
						 
					}
				
			});
			
			self::rate_all();
			
			return true;
	}
	
	//get Teams
	public static function getTable():array {
			return self::$teams;
	}
	
	
	//restore Teams from session
	public static function loadTeamsFromSession() {
		self::$teams = request()->session()->get('teams');
	}
	
	//save Teams to session
	public static function saveTeamsToSession() {
		 request()->session()->put('teams', self::$teams);
	}
		
}

?>