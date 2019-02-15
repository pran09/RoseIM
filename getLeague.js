$(function () {

	$sport = $('#sport');
	$league = $('#league');

	$sport.on('change', function() {
		var sport = $sport.val();
		console.log(sport);
		$.ajax( {
			type: 'GET',
			url: 'http://roseim.csse.rose-hulman.edu/RoseIM/getLeague.php',
			data: {sport: $sport.val()},
			datatype: "html",
			success: function() {
				console.log('hi');
				//$league.append(leagues);
			},
			error: function() {
				alert('error loading leagues');
			}
		});
	});
});