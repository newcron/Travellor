<?php header("Content-Type: text/xml"); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<kml xmlns="http://earth.google.com/kml/2.2">
<Document>


<?php
define("DATA_FILE_DIR", "datafiles/"); 
$file = $_GET["datafile"]; 

$result = stripslashes(file_get_contents(DATA_FILE_DIR.$file, $data)); 
$result = "$result"; 
echo "<name>$file</name>";
$jso = json_decode($result, true); 

foreach($jso as $item) {
$title=htmlspecialchars($item["title"]); 
$addr=htmlspecialchars($item["address"]); 
$lat=$item["lat"]; 
$lng=$item["lng"]; 
echo <<<end
	<Placemark>
		<name>$title</name>
		<description><![CDATA[$addr]]></description>
		<Point>
			<coordinates>$lng,$lat,0</coordinates>
		</Point>
	</Placemark>

end;
}

?>
</Document>
</kml>