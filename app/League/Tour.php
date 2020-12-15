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
			$games = [];			
			foreach ($this->matches as $g) {
					$games[] = Array('host' => $g->getTeam1()->get_name(), 'guest' => $g->getTeam2()->get_name(), 'score1' => $g->getScore1(),'score2' => $g->getScore2()) ;
			}
			
			return $games;
	}
	
	public function cancelTour() {
		
	}

	
	
	//Playing a tour
	private function play():void {
		
		foreach($this->games as $g) {
			$m = new Match($g['host']), $g['guest']);
			
			$this->matches_by_team[$g['host']->get_index()][] = $m;
			$this->matches_by_team[$g['guest']->get_index()][] = $m;
			
			$this->matches[] = $m;
		}
		
		//Teams::sortTable();
		
	}
	
	
}
	
?>