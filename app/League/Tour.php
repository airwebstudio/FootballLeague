<?php
//Class for save and manage Each Tour

namespace App\League;


class Tour {
		
	private $games;
	private $matches;
	private $matches_by_team;
	
	
	public function __construct(array $games) {
		if (empty($games)) {
			throw new \Exception('No games for a tour');	
		}
			
		$this->games = $games;
		$this->play();
		
	}
	
	//get Matches of this Tour
	public function getMatches() {
		
			if (empty($this->matches)) {
				throw new \Exception('No data');	
			}
			
			$games = [];			
			foreach ($this->matches as $g) {
					$games[] = Array('host' => $g->getHost()->get_name(), 'guest' => $g->getGuest()->get_name(), 'score1' => $g->getScore1(),'score2' => $g->getScore2()) ;
			}
			
			return $games;
	}
	
	public function cancelTour() {
		
	}

	
	
	//Playing a tour
	private function play():void {
		
		foreach($this->games as $g) {
			$m = new Match($g['host'], $g['guest']);
			
			$this->matches_by_team[$g['host']->get_index()][] = $m;
			$this->matches_by_team[$g['guest']->get_index()][] = $m;
			
			$this->matches[] = $m;
		}
		
		//Teams::sortTable();
		
	}
	
	
}
	
?>