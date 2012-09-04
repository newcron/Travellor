<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
<Document>
  <Placemark>
    <name>Zürich</name>
    <description>Zürich</description>
    <Point>
      <coordinates>8.55,47.3666667,0</coordinates>
    </Point>
  </Placemark>

<?php
define("DATA_FILE_DIR", "datafiles/"); 
$file = $_GET["datafile"]; 

$result = stripslashes(file_get_contents(DATA_FILE_DIR.$file, $data)); 
$result = "$result"; 

$jso = json_decode($result, true); 
var_dump($jso); 
foreach($jso as $item) {
echo <<<end
	<Placemark>
		<name></name>
		<description></description>
	</Placemark>
end;
}

?>
</Document>
</kml>