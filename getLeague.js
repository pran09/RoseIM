$(function () {

	$sport = $('#sport');
	$league = $('#league');

	$sport.on('change', function() {
		var sport = $sport.val();
		console.log(sport);
		$.ajax( {
			type: 'POST',
			url: 'getLeague.php',
			data: sport,
			datatype: "html",
			success: function(leagues) {
				// $league.append('<label>League:</label>');
				// $league.append('<select name="League" id="league"');
				// $.each(leagues, function(i, league) {
				// 	var thisLeague = league[i];	
				// 	$league.append('<option value="' + thisLeague + '">' + thisLeague + '</option>');
				// });
				// $league.append('</select>');
				$league.append(leagues);
			},
			error: function() {
				alert('error loading leagues');
			}
		});
	});
});