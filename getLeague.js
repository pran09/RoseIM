$(function () {

	$sport = $('#sport');
	$league = $('#league');

	// $sport.on('change', function() {
	// 	var sport = $sport.val();
	// 	console.log(sport);
	// 	$.ajax( {
	// 		type: 'POST',
	// 		url: 'getLeague.php',
	// 		data: {sport: $sport.val()},
	// 		datatype: "html",
	// 		success: function(leagues) {
	// 			$league.append(leagues);
	// 		},
	// 		error: function() {
	// 			alert('error loading leagues');
	// 		}
	// 	});
	// });


	var settings = {
  		"async": true,
 		"crossDomain": true,
  		"url": "http://roseim.csse.rose-hulman.edu/RoseIM/getLeague.php?league=Basketball",
  		"method": "POST",
  		"headers": {
    	"cache-control": "no-cache",
    	"Postman-Token": "0f9ceda4-e6af-4795-ae85-295750fdc217"
  		}
	}

	$.ajax(settings).done(function (response) {
  		console.log(response);
	});s
});