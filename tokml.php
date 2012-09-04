<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<kml xmlns="http://www.opengis.net/kml/2.2">
<Document>


<?php
define("DATA_FILE_DIR", "datafiles/"); 
$file = $_GET["datafile"]; 

$result = stripslashes(file_get_contents(DATA_FILE_DIR.$file, $data)); 
$result = "$result"; 

$jso = json_decode($result, true); 

foreach($jso as $item) {
$title=$item["title"]; 
$addr=$item["address"]; 
$lat=$item["lat"]; 
$lng=$item["lng"]; 
echo <<<end
	<Placemark>
		<name>$title</name>
		<description>$addr</description>
		<Point>
			<coordinates>$lat,$lng,0</coordinates>
		</point>
	</Placemark>
end;
}

?>
</Document>
</kml>