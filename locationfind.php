<!doctype html>
<html>
  	<head>
		<meta content="yes" name="apple-mobile-web-app-capable" />
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
		<!-- iWebKit css follows -->
		<link href="style.css" rel="stylesheet" media="screen" type="text/css" />
		<link rel="apple-touch-icon" href="http://YOURURL/locate.png"/>
		<script src="javascript/functions.js" type="text/javascript"></script>
		<title>Locate me</title>
		<meta content="keyword1,keyword2,keyword3" name="keywords" />
		<meta content="Description of your page" name="description" />	
		<script>
		
		navigator.geolocation.getCurrentPosition(foundLocation, noLocation);
		geo='';
		 function foundLocation(position)
		 {
		   lat = position.coords.latitude;
		   long = position.coords.longitude;
		   		  
		  ulk ="test";
		  geo = ''+lat + ',' + long+'';
		  document.getElementById('geolocation').innerHTML=geo;
		  locurl="?loc="+lat + "," + long;
		  
		  document.getElementById('loc').value=lat + ',' + long;
		  
		  mapwrite='<a href="comgooglemaps://?center='+lat+','+long+'&amp;zoom=15&amp;views=satellite"><span class="name">In Maps öffnen</span></a>';
		  
		  document.getElementById('maps').innerHTML=mapwrite;
		  
		   
		 }
		 function noLocation()
		 {
		   document.write('Could not find location');
		 }
		</script>
		</head>
	<body>
		<div id="topbar">
		    <div id="title">Locate me</div> 

		 </div>
		<div id="content">
      
			<ul class="pageitem">
      
		
				<li class="bigfield"><span class="name" id="geolocation"></span></li>
				
			</ul>
	
			
			<form method="post" enctype="multipart/form-data">
				
			
				<span class="graytitle">Route auswählen:</span>
			<ul class="pageitem">
				<li class="select">
					<select name="dir">
						<?php
						
					if(!empty($_REQUEST["dir_priority"])) {
					  	  $pos1 = strpos($_REQUEST["dir_priority"],".");
						  $pos2 = strpos($_REQUEST["dir_priority"],"/");


					  	  if($pos1 === false && $pos2 === false) {
				  			if (!file_exists('all_locations/'.$_REQUEST["dir_priority"]) and !is_dir('all_locations/'.$_REQUEST["dir_priority"])) {
				  			   $oldmask = umask(0);
							    mkdir('all_locations/'.$_REQUEST["dir_priority"], 0777); 
								umask($oldmask);        
				  			} 
					  	  }
					  }
					  
						
						
						$file_array = array();
						$filemtime_array = array();
						if ($handle = opendir('all_locations')) {
						    while (false !== ($file = readdir($handle))) {
						        if ($file != "." && $file != "..") {
						  	     
								  $file_array[]=$file;
						  		  $filemtime_array[] = filemtime('/YOURPATH/all_locations/'.$file.'/.');
						  	  
						        }
						    }
					  	  array_multisort($filemtime_array, SORT_DESC, $file_array);
							
						    closedir($handle);
						}
						if(!empty($_REQUEST["dir_priority"])){
							$zusatzordner = $_REQUEST["dir_priority"];
						}
						else {$zusatzordner=$_REQUEST["dir"];}
						foreach($file_array as $filename){
							if ($zusatzordner==$filename) {$zusatz = " selected='selected'";}
									else {$zusatz = "";}
							echo "<option$zusatz>$filename</option>";
						}
						?>
					</select><span class="arrow"></span>
				</li>
			</ul>
			<span class="header" style="padding-left:10px;">... oder neu eingeben:</span>
			<ul class="pageitem">
				<li class="bigfield"><input placeholder="Route angeben" type="text" name="dir_priority" autocorrect="off"></li>
			</ul><br><span class="graytitle">Foto hochladen:</span>
			<ul class="pageitem">
				<li class="bigfield"><input placeholder="Foto hochladen" type="file" name="foto1"></li>
			</ul>
			
			<ul class="pageitem">
				<input type="hidden" name="loc" id="loc" />
				<li class="button">
				<input name="Submit input" type="submit" value="Position speichern" />
			</li>				
			</ul>
		</form>
			
			
		</div>
		<?php
			
		$erfolg=false;
			
		if(!empty($_REQUEST["loc"])) {

		if(!empty($_REQUEST["dir_priority"])) {
		  	  $pos1 = strpos($_REQUEST["dir_priority"],".");
			  $pos2 = strpos($_REQUEST["dir_priority"],"/");


		  	  if($pos1 === false && $pos2 === false) {
		  		  $ordner = $_REQUEST["dir_priority"];
		  	  }
		  	  else {
		  		  die("<p>Dateiname nicht erlaubt.</p>");
		  
		  	  }
		  }
				
		else {
  		  	  $pos1 = strpos($_REQUEST["dir"],".");
  			  $pos2 = strpos($_REQUEST["dir"],"/");


  		  	  if($pos1 === false && $pos2 === false) {
  		  		  $ordner = $_REQUEST["dir"];
  		  	  }
  		  	  else {
  		  		  die("<p>Dateiname nicht erlaubt.</p>");
		  
  		  	  }
		}
		
			$file = 'location.txt';
			// Open the file to get existing content
			$current = htmlspecialchars($_REQUEST["loc"]);
			// Write the contents back to the file
			if(file_put_contents($file, $current)) {
				
						echo "<p>Erfolgreich geschrieben.</p>";
				
			}
			$timestamp = time();
			$datum = date("d.m.Y",$timestamp);
			$uhrzeit = date("H-i",$timestamp);
			$t2 = $datum."_".$uhrzeit."_Uhr";
			$file2 = 'all_locations/'.$ordner.'/location_'.$t2.'.txt';
			file_put_contents($file2, $current);
			

	  	 if(!empty($_FILES["foto1"])) {
  			if (!file_exists('all_pics/'.$ordner) and !is_dir('all_pics/'.$ordner)) {
  			   $oldmask = umask(0);
				mkdir('all_pics/'.$ordner, 0777);     
			  umask($oldmask); 
  			} 
  			if (!file_exists("all_pics/$ordner/$current") and !is_dir("all_pics/$ordner/$current")) {
   			   $oldmask = umask(0);
			    mkdir("all_pics/$ordner/$current", 0777); 
				umask($oldmask); 	        
  			} 
	  	  $uploaddir = "/YOURPATH/all_pics/$ordner/$current/";
	  	  $uploadfile = $uploaddir . mt_rand() . '_'. basename($_FILES['foto1']['name']);
		  
		  
		  // valid image?
		  if($_FILES["foto1"]["size"] >= 10120000) {
		    echo "F2";
		    die("Foto zu groß.");
		  } else {
		      $imageData = @getimagesize($_FILES["foto1"]["tmp_name"]);

		      if($imageData === FALSE || !($imageData[2] == IMAGETYPE_GIF || $imageData[2] == IMAGETYPE_JPEG || $imageData[2] == IMAGETYPE_PNG)) {
		        echo "F2";
		        die("Foto kein Bild.");
		      }
		  }
		  
		  
		  
	  	  echo '<pre>';
	  	  if (move_uploaded_file($_FILES['foto1']['tmp_name'], $uploadfile)) {
	  	      echo "Datei ist valide und wurde erfolgreich hochgeladen.\n";
	  	  } else {
	  	      echo "Möglicherweise eine Dateiupload-Attacke!\n";
	  	  }

	  	  echo 'Weitere Debugging Informationen:';
	  	  print_r($_FILES);

	  	  print "</pre>";
	    }
			
			
	
			die("</div></body>\n</html>");
			
		}
			
		?>

		
	
</div>
		
	</body>
</html>
