<?php
//Class for Creation a Time table for Tours

namespace App\League;

class Match_list {
		
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
						
						$match = Array('owner' => $i1, 'guest' => $i2);
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
		
		
		
	
}

?>