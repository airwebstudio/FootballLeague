<?php 
namespace App\League;

class TimeTableGen {
	
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
	
}


?>