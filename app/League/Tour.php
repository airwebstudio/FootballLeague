<?php
//Class for save and manage Each Tour

namespace App\League;


class Tour {
		
	private $games;
	private $matches;
	private $matches_by_team;
	
	public function __construct(array $games) {
		if (!empty($games)) {
			$this->games = $games;
			$this->play();
		}
		return false;
	}
	
	//get Matches of this Tour
	public function getMatches() {
			return $this->matches;
	}

	
	
	//Playing a tour
	private function play():void {
		
		foreach($this->games as $g) {
			$m = new Match(Teams::get($g['owner']), Teams::get($g['guest']));
			$this->matches_by_team[$g['owner']][] = $m;
			$this->matches_by_team[$g['guest']][] = $m;
			
			$this->matches[] = $m;
		}
		
		Teams::sortTable();
		
	}
	
	
}
	
?>