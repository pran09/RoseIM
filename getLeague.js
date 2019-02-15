$(function () {

	$sport = $('#sport');
	$league = $('#league');

	$sport.on('change', function() {
		var sport = $sport.val();
		console.log(sport);
		$.ajax( {
			type: 'POST',
			url: 'getLeague.php',
			data: {sport: $sport.val()},
			datatype: "html",
			success: function(leagues) {
				$league.append(leagues);
			},
			error: function() {
				alert('error loading leagues');
			}
		});
	});
});