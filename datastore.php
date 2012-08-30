<?php
$data = $_POST["content"]; 
$result = @file_put_contents("datafile.json", $data); 
if($result === false) {
	http_response_code(500); 
}
?>