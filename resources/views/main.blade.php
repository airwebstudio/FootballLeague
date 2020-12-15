<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Football league immitation</title>

  <link rel="stylesheet" href="/css/style.css">

  
  
  <!-- Latest compiled and minified CSS -->
<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous"></script>
  


  

 <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>


</head>

<body data-token="{{csrf_token()}}">
	<div id="page">
	
		<script src="js/index.js"></script>
		
		<h4 class="text-secondary">Github <a href="https://github.com/airwebstudio/FootballLeague" target="_blank">link</a> to repository of source codes</h4>
		<hr/>
	  
		<div id="add_teams_container" data-page="add_teams" style="display:none">
			<h2>ADD TEAMS TO FOOTBALL LEAGUE!</h2><p><b>Minimum 4 teams. </b> Each team should have an unique name!</p>
		</div>
		
		
		<div id="league_screen" data-page="league" style="display:none">
			
			<div class="top">
			
			<div id="league">				
			
					  <h2>League table</h2>
					  <table class="table table-striped" id="league_table">
						  <thead>
							<tr>
							  <th scope="col">TEAM</th>
							  <th scope="col">GMS</th>
							  <th scope="col">S</th>
							  <th scope="col">W</th>
							  <th scope="col">D</th>
							  <th scope="col">L</th>
							  <th scope="col">GD</th>
							</tr>
						  </thead>
						  <tbody>
								
							
						  </tbody>
						  
					  </table>
					  
					  <div class="buttons">
						  <div class="form-group">
							<button class="btn btn-primary" id="next_week">Next week</button>
							<button class="btn btn-primary" id="next_all">Play all</button>
						  </div>
						  
						  
						  <div class="form-group">
								<button id="reset_same" class="btn btn-success">Reset with the same teams</button>
								
								<button id="reset_all" class="btn  btn-success">Reset with a new teams</button>
						</div>
						  
						  
					 </div>
			 
					</div>
					
					
					
					<div id="games">
						<h2><span class="week_num"></span>th Week Match Result</h2>
						
						<table class="table table-striped" id="games_table">
							  <thead>
							  
							  </thead>
							  
							  <tbody>
							  
							  </tbody>
						  
						 </table>
					</div>
					
					<div id="predicts">
						<h2><span class="week_num"></span>th Week Predictions of Championship</h2>
						
						
						<table class="table table-striped" id="predicts_table">
							  <thead>
							  
							  </thead>
							  
							  <tbody>
							  
							  </tbody>
						  
						 </table>
					</div>
				</div>
			
			<div id="next_games">
				<h1> Next games list</h1>
				
				<div>
				
				</div>
			</div>
		</div>
		
		
  
  
</body>
</html>