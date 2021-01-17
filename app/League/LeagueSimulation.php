<?php
namespace App\League;
	
class LeagueSimulation {	

	private $lg;
	
	public function startNew(array $teams):League {   //Start new League
		
			return $this->lg = new League($teams);	
			
	}	
	
	public function getCurrent() {   //Get current League from Session
		
		$this->lg = \MyStorage::load('lg');
		if (!isset($this->lg)) {
			throw new \Exception('New League', 503);
		}
		
		return $this->lg;			
	}
	
	//Clear Team least
	public function clear($all = false) {
		
		$this->lg = \MyStorage::load('lg');
		
		if (!$all)
			return $this->lg = new League($this->lg->get_teams_names());
		else {
			
			\MyStorage::delete('lg');
			throw new \Exception('New League', 503);
			
		}
		 
	}
	
}

?>