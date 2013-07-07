<?php 
if (isset($_REQUEST["wohidehinweis"])) {
  $hinweis_anzeigen = false;
}
else {
	setcookie("wohidehinweis", "verstecke Hinweis", time()+3600*24*30);
	$hinweis_anzeigen=true;
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<link href='http://fonts.googleapis.com/css?family=Vollkorn' rel='stylesheet' type='text/css'>
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map-canvas { height: 100% }
    </style>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=YOURAPIKEY&sensor=true&libraries=geometry">
	  
	  
	  <?php 




	  
	  
	  
	  $station = htmlspecialchars($_GET['station']);
	  $station_ordner = "";
	  $pos1 = strpos($station,".");	  $pos2 = strpos($station,"/");


	  if($pos1 === false && $pos2 === false) {
		  $station_ordner = $station;
	  }
	  else {
		  die("<p>Dateiname nicht erlaubt.</p>");
		  
	  }
	  
	  $log_directory = "/YOURPATH/all_locations/$station_ordner";
	  $file_array = array();
	  $filemtime_array = array();
	  foreach(glob($log_directory.'/*.*') as $file) {
	      $file_array[]=$file;
		  $filemtime_array[] = filemtime($file);
	  }
	  array_multisort($filemtime_array, SORT_ASC, $file_array);
	  $content_array=array();
	  $content_uncensored = array();
	  foreach ($file_array as $file) {
		  $filecontent = file_get_contents($file);
		  $content_uncensored[] = $filecontent;
		  $temp = explode(",",$filecontent);
		  $content = round($temp[0], 5) . "," . round($temp[1], 5);
		  $content_array[] = $content;
		  
	  }
	  ?>
    </script>
    <script type="text/javascript">






	function initialize() {
	  var myLatLng = new google.maps.LatLng(<?php echo $content_array[0]; ?>);
	  var mapOptions = {
	    zoom: 9,
	    center: myLatLng,
	    mapTypeId: google.maps.MapTypeId.TERRAIN
	  };

	  var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);



	  var flightPlanCoordinates = [
	 	 <?php
		  foreach ($content_array as $content) {
			  if($content!=$content_array[0]){echo ",\n";}
			  echo "new google.maps.LatLng($content)";
		  }
	  	?>
	  ];
	  

	  
	  
	  var flightPath = new google.maps.Polyline({
	    path: flightPlanCoordinates,
	    strokeColor: '#FF0000',
	    strokeOpacity: 1.0,
	    strokeWeight: 2
	  });
	  var distance = google.maps.geometry.spherical.computeLength(flightPlanCoordinates);
	  gesamtlaenge = Math.round(distance/1000) + " km";
	  flightPath.setMap(map);
	  
	  
	  
 	 <?php
	  foreach ($content_array as $key=>$content) {
		  $image_array = array();
		  $imagemtime_array = array();
		  foreach(glob("/YOURPATH/all_pics/$station_ordner/$content_uncensored[$key]/*.*") as $image) {
		      $image_array[]=$image;
			  $imagemtime_array[] = filemtime($image);
		  }
		  array_multisort($filemtime_array, SORT_ASC, $file_array);
		  
		  $i1 = $image_array[0];
		  
		  
		  echo "
			  
	    var infowindow$key = new google.maps.InfoWindow({
	        content: '<div style=\"font-family: Vollkorn, Sans; ";if(!empty($i1)){echo "min-height:265px";}echo "\">";if(!empty($i1)){echo "<img src=\"http://YOURURL/all_pics/$station_ordner/$content_uncensored[$key]/".basename($i1)."\" width=\"265\" style=\"padding-top:10px;\" /><br>";}echo"Position: $content<br>am:  " . date ("d.m.Y, H:i:s ", filemtime($file_array[$key])) . "Uhr</div>'
	    });
			  
			  
	      var marker$key = new google.maps.Marker({
	          position: new google.maps.LatLng($content),
	          map: map";
			  if($key==0){
				  echo ",
				  icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
				  	";
			  }
	     echo " });
		  
	      google.maps.event.addListener(marker$key, 'click', function() {
	        infowindow$key.open(map,marker$key);
	      });
			  ";
	  }
  	?>
	
	
  var bounds = new google.maps.LatLngBounds();
  for (var i = 0; i < flightPlanCoordinates.length; i++) {
      bounds.extend(flightPlanCoordinates[i]);
  }
	map.fitBounds(bounds);
	  	  
    var homeControlDiv = document.createElement('div');
    var homeControl = new HomeControl(homeControlDiv, map);

    homeControlDiv.index = 1;
    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);

	  
	}
	
  function hide_hinweis(){
	  document.getElementById('hinweis').style.display="none";
  }
	
	function HomeControl(controlDiv, map) {

	  // Set CSS styles for the DIV containing the control
	  // Setting padding to 5 px will offset the control
	  // from the edge of the map
	  controlDiv.style.padding = '5px';

	  // Set CSS for the control border
	  var controlUI = document.createElement('div');
	  controlUI.style.backgroundColor = 'white';
	  controlUI.style.borderStyle = 'solid';
	  controlUI.style.borderWidth = '2px';
	  controlUI.style.cursor = 'default';
	  controlUI.style.textAlign = 'center';
	  controlUI.title = '';
	  controlDiv.appendChild(controlUI);

	  // Set CSS for the control interior
	  var controlText = document.createElement('div');
	  controlText.style.fontFamily = 'Arial,sans-serif';
	  controlText.style.fontSize = '12px';
	  controlText.style.paddingLeft = '4px';
	  controlText.style.paddingRight = '4px';

	  controlText.innerHTML = '<?php if($hinweis_anzeigen==true){echo "<div id=\"hinweis\" style=\'font-family:Vollkorn, sans-serif; display:inline;\'><div style=\'text-align:left;\'><h1>Willkommen bei wo.ist.fapeg!</h1><p>Auf dieser Karte siehst du die Route <b>$station_ordner</b>.<br>Klicke auf einen Marker, um genauere Infos zum Standort zu erhalten.</p></div><p><span style=\"cursor:pointer; text-decoration:underline; color:blue;\" onclick=\" hide_hinweis(); \">Hinweis schließen</a></p></div>";  } ?><b>Gesamtstrecke:</b> ' +gesamtlaenge;
	  controlUI.appendChild(controlText);



	}

	

	google.maps.event.addDomListener(window, 'load', initialize);

    </script>
	<title>Route durch <?php echo htmlspecialchars($_GET["station"]); ?></title>
  </head>
  <body>
    <div id="map-canvas"/>
  </body>
</html>
