// function getTeams(leagueID) {
// 	$teams = $('#team');
// 	$.ajax( {
// 		type: 'GET',
// 		url: 'http://roseim.csse.rose-hulman.edu/RoseIM/getTeams.php',
// 		data: {league: leagueID},
// 		datatype: "html",
// 		success: function(teams) {
// 			$teams.append(teams);
// 		}
// 		error: function() {
// 			alert('error loading teams');
// 		}
// 	});
// };


$(function () {

	$sport = $('#sportDiv');
	$league = $('#league');

	$sport.on('change', function() {
		var sport = $sport.val();
		console.log(sport);
		$.ajax( {
			type: 'GET',
			url: 'http://roseim.csse.rose-hulman.edu/RoseIM/getLeague.php',
			data: {sport: $sport.val()},
			datatype: "html",
			success: function(leagues) {
				console.log(leagues);
				$league.append(leagues);
			},
			error: function() {
				alert('error loading leagues');
			}
		});
	});

	$leagueMenu = $('#leagueSelect');
	$leagueMenu.on('change', function() {
		console.log('league selected');
	});
});