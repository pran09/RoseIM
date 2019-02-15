$(function () {

	$sport = $('#sport');
	$league = $('#league');

	$sport.on('change', function() {
		var sport = $sport.val();
		console.log(sport);
		$.ajax( {
			type: 'GET',
			url: 'getLeague.php',
			data: {sport: $sport.val()},
			datatype: "html",
			done: function(leagues) {
				$league.append(leagues);
			},
			fail: function() {
				alert('error loading leagues');
			}
		});
	});
});