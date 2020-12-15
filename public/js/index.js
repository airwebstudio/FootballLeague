//Front end JS to communicate with Server. jQuery and ajax 

$(document).ready(function() {
	//token for ajax request
	var token = $('body').data('token');
	
	_server = {
		getTable: function(next) {
			
			//getting info from server
			$.post('/get-table', {'_token': token, 'next': next}, 'json')			
						
			.then(
				function(data) {	
					
					
					//show and hide different panels
					if (data['league_status'] == 'new') {							
							
						$('#predicts').hide();	
						$('#games').hide();
						
					}
					else {
						$('#games').show();
						
						if (data['league_status'] != 'finished') {
							$('#predicts').show();	
						}
						else {
							$('#predicts').hide();
						}
					}
					//Change week number					
					$('.week_num').text(data.tour_played);
					
					//Fill up tables
					
					//League
					if (data['league_table']) {
						
						$('.num').text(data['tour_num'])
						
						_table.fill_league_table(data['league_table'])
							
						$('#league').show();
						
						
					}
					
					//Games of lastTour
					if (data['games_table']) {
						_table.fill_games_table(data['games_table']);
					}
					
					
					//Predicts
					if (data['predicts_table']) {
						_table.fill_predicts_table(data['predicts_table']);
					}
					
					//Next games annonce
					if (data['next_games']) {
						_table.fill_next_games(data['next_games']);
					}
					
					
					//load League part
					_app.load('league');
					
				
				
			},			
			//MY NO-TEAMS ERROR
			function(jqXHR, textStatus, errorThrown){
				
				if (jqXHR.status == 503) {
					
					
					//open add teams form					
					_table.gen_add_teams_form($(_vars.add_teams_container));
										
										
					//
					_app.load('add_teams');
					
					
					
				}
			}
			);
		},
		//upload teams
		sendTeams: function(data) {			
			return $.ajax({url: '/up-teams', 'async': false, 'data': data, 'type': 'POST', 'dataType': 'json'});			
		}
		
		
	}
	
	_app = {
			init: function() {
					$('[data-page]').hide();
			},
			
			load: function(name) {
					this.init();
					
					$('*[data-page='+name+']').show();
			}
	}
	
	//some consts
	_vars = {
		 min_teams_count: 4,
		 add_teams_container: '#add_teams_container'
	},
	
	//gen form object
	_form = {
			gen_input: function(params) {
					return $("<div></div>", {'class': 'form-group'}).append($("<input>", params));
			}
	},
	
	//fill tables object
	_table = {
		
		//League table
		fill_league_table: function(data) {
			
			$('#league_table tbody').html('');
			
			for (ind in data) {
				let d = data[ind];
				
				let new_row = $('<tr/>');
				new_row.append($('<th/>', {'scope': 'row'}).append(d.name));
				new_row.append($('<td/>').append(d.games));
				new_row.append($('<td/>').append('<b>' +  d.scores + '</b>'));
				new_row.append($('<td/>').append(d.wins));
				new_row.append($('<td/>').append(d.draws));
				new_row.append($('<td/>').append(d.looses));
				new_row.append($('<td/>').append(d.goals));
				
				
				
				$('#league_table tbody').append(new_row);
			}
		},
		
		//Games table
		fill_games_table: function(data) {
			
			$('#games_table tbody').html('');
			
			for (ind in data) {
				let d = data[ind];
				
				let new_row = $('<tr/>');
				
				new_row.append($('<td/>').append(d.host));
				new_row.append($('<td/>').append('<b class="nowrp">' + d.score1 + ':' + d.score2 + '</b>'));
				new_row.append($('<td/>').append(d.guest));
				
				
				
				
				$('#games_table tbody').append(new_row);
			}
		},
		
		//Predicts table
		fill_predicts_table: function(data) {
			
			$('#predicts_table tbody').html('');
			
			for (ind in data) {
				let d = data[ind];
				
				let new_row = $('<tr/>');
				
				new_row.append($('<td/>').append(d.name));
				new_row.append($('<td/>').append(d.chance + '%'));
				
				
				$('#predicts_table tbody').append(new_row);
			}
		},
		
		//Next games table
		fill_next_games: function(data) {
			
			$('#next_games > div').html('');
			
			for (i1 in data) {
				$('#next_games > div')
				
				let div = $('<div/>');

				
				div.append('<h3>' + i1 + 'th Week</h3>');
				
				
				for (i2 in data[i1]) {
					
					div.append('<p>' + data[i1][i2]['host'] + ' - ' + data[i1][i2]['guest'] + '</p>');
				}	

				$('#next_games > div').append(div);
				
			}
			
			
		},
		
		form: null,
		
		//Generation Add team form
		gen_add_teams_form: function(where) {
			
			
			this.form = $("<form/>", 
                 { type:'POST' }
            );
			
			var added_teams = [];
			
			this.area = $("<div/>", {'id': "input_added_teams"});
			
			
			this.form.append(this.area);
			
			
			//for (let i = 0; i < _vars.min_teams_count; i++) {
				
				this.form.append(_form.gen_input({'type': "text", "id": "input_team", 'placeholder': 'Input team name', class: "form-control"}));
				//this.form.append(_form.gen_input({'type': "text", "required": "required", 'name': 'teams[]', 'placeholder': 'Input team name', class: "form-control"}));
			//}
			
			
			this.form.append(_form.gen_input({"type": "submit", "value": "Add team", "class": "btn", "id": "add_team"}));
			
			this.form.append(_form.gen_input({"type": "button", "value": "Start League!", 'disabled': 'disabled', "id": "submit_teams", "class": "btn btn-primary"}));
			this.form.append('<p>*Minimum 4 Teams</p>');
			
			//this.form.append(_form.gen_input({"type": "hidden", "value": token, 'name': '_token',  "class": "btn btn-primary", "id": "send_form"}));
			
			//PUT TO $WHERE
			where.find('form').remove();
			where.append(this.form);
			
			
			//EVENTS
			$(this.form).on('click', '#submit_teams', function(){
				
					let arr = [];
					
					let teams = $(this).closest('form').find('[data-team]');
					
					if (teams.lenght < _vars.min_teams_count) {
							alert('ERROR!');
							return false;
					}
					
					for (let i in teams) {
						arr.push(teams.eq(i).data('team'));
					}
					
					
					let data = _server.sendTeams({'teams': arr, '_token': token});
					
					if (data) {
						_server.getTable('');
							
					}
					
					return false;
					
			});
			
			$(this.form).on('submit', function() {
				
				
					let nm = $('#input_team').val();
					
					
					if ((nm == '') || ($('[data-team="'+nm+'"]').length > 0)) {
							alert('Dublicate name! Each team should to have unique name');
							
							return false;
					}
					let div = $('<div/>', {'data-team': nm});
					let a_in = $('<a/>', {'class': 'remove_item', 'href': '#'});
					
					
					
					a_in.append('Remove');
					
					div.append(nm);
					div.append(a_in);
					
					$('#input_added_teams').append(div);
					
					
					$('#submit_teams').prop('disabled', ($('[data-team]').length < _vars.min_teams_count));
						
					
					
					$('#input_team').val('');
					$('#input_team').focus();
				
					//$(this).before(_form.gen_input({'type': "text", "required": "required", 'name': 'teams[]', 'placeholder': 'Input team name', class: "form-control"}));
					
					return false;
					
			});
			
			
			$(this.form).on('click', '.remove_item', function() {
				
				$(this).closest('[data-team]').remove();
				
				$('#submit_teams').prop('disabled', ($('[data-team]').length < _vars.min_teams_count));
				
				//$('#input_team').val('');
				$('#input_team').focus();
			});
			
		}, 			
		
	}

	//start app		
	_server.getTable('');
	
	
	
	//GLOBAL EVENTS
	$('body').on('click', '#next_week', function(){
		_server.getTable('week');
	});
	
	 $('body').on('click', '#next_all', function(){
			_server.getTable('all');
	});
	
	$('body').on('click', '#reset_same', function(){
			_server.getTable('reset');
	});
	
	 $('body').on('click', '#reset_all', function(){
			_server.getTable('reset_all');
	});
	


		
	
	
	
});

