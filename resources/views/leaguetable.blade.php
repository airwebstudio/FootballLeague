<table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">TEAM</th>
      <th scope="col">GMS</th>
      <th scope="col">SCORES</th>
      <th scope="col">WINS</th>
    </tr>
  </thead>
  <tbody>
		
	@foreach ($teams as $team)
		<tr class="team_{{$team->get_index()}}">
			<th scope="row">{{$team->get_name()}}</th>
			<th>{{$team->get_matches_count()}}</th>
			<th>{{$team->get_scores()}}</th>
			<th>{{$team->get_wins()}}</th>
		</tr>
	@endforeach
  </tbody>
  
 </table>
  