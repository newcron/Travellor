<?php
define("DATA_FILE_DIR", "datafiles/"); 
$data = $_POST["content"]; 
$file = $_POST["datafile"]; 
$result = @file_put_contents(DATA_FILE_DIR.$file, $data); 
if($result === false) {
	http_response_code(500); 
}
?>