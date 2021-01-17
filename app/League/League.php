<?php
//Main league singleton Class
namespace App\League;

class League extends LeagueBaseWithTable {
	
	public function __construct(array $teams) {		
		parent::__construct($teams);
		$this->nextWeek();		
	}
	
	public function nextWeek() { //Play next Tour
		
		if ($this->ToursToPlay() == 0)
			return false;
		
		$t = new Tour($this->tours_list[sizeof($this->tours)]);
		
		$matches = $t->getMatches();
		$this->tours[] = $t;
		$this->sortTable();
		
		$this->saveCurrent();
				
		return $t;
		
	}
	
	public function playAll() {
		
		while($this->nextWeek()) {}		
		
	}
}

?>