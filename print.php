<html><head>
<style type="text/css">
body {font-family: sans-serif; font-size: 8pt;}
</style>
</head><body>

<table>
<?php
define("DATA_FILE_DIR", "datafiles/"); 
$file = $_GET["datafile"]; 
$colorsCounts = array("Red" => 1, "Green" => 1, "Yellow" => 1, "Blue" => 1); 
$result = stripslashes(file_get_contents(DATA_FILE_DIR.$file, $data)); 
$result = "$result"; 
$jso = json_decode($result, true); 
$i=0; 
foreach($jso as $item) {
	$title=$item["title"];  
	$addr=$item["address"]; 
	$color= $item["color"]; 
	
	if($i%3 == 0) {
		echo "<tr>"; 
	}
	$cnt = $colorsCounts[$color]++; 
	echo<<<end
	<td style="background: $color; color: black; font-weight: bold; text-align: center ">$cnt</td>
	<td>$title<br>$addr</td>
end;


	if($i % 3 == 2){
		echo "</tr>"; 	
	}
	$i++; 
}

?>
</table>
</body></html>