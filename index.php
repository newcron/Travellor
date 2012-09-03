<!DOCTYPE html>
<?php

define("DATA_FILE_DIR", "datafiles/"); 
$datafile = $_GET["datafile"]; 


$dh  = opendir(DATA_FILE_DIR);
while (false !== ($filename = readdir($dh))) {
	if($filename[0]!=".") {
	    $datafiles[] = $filename;
	}
}

$datafile = $datafile != null  ? $datafile : $datafiles[0]; 


$datafileExpl = explode(".",$datafile); 
$country = $datafileExpl[sizeof($datafileExpl)-2]; 

$style = $_GET["print"] == true ? "print.css" : "style.css"; 
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="json2.js"></script>
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
    <script type="text/javascript" src="travellor.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php print $style;?>">
</head>
<body>
	<div id="maps-area">
	</div>
	<div id="markers-area">
		<div class="titlebar">
			<a href="#" id="action-hide">_</a>
			<strong>Travellor</strong>
			<a href="#" id="action-save">Save</a>

		</div>
		<div id="contentarea">		
			<form class="new-item">
				<input type="text" id="new-item-title" placeholder="Title" /><br/>
				<input type="text" id="new-item-address" placeholder="Address" /><br/>
				<select id="new-item-color">
					<option>Blue</option>
					<option>Green</option>
					<option>Red</option>
					<option>Yellow</option>
				</select>
				
				<button type="button" id="new-item-preview" class="btn btn-primary">Search</button>
				
				
				<p id="new-item-confirm-alert" >
					Is this the location you are looking for? <br/>
					<button type="button" id="preview-confirm">Yes</button>
					<button type="button" id="preview-cancel">No</button>
				</p>
			</form>

			<div id="address-table-wrapper">
				<table class="table table-striped" id="address-table">
					<thead>	
						<tr>
							<th>Icon</th>
							<th>Location</th>
						</tr>
					</thead>
					<tbody id="data-table-body">			
					</tbody>
				</table>
			</div>
			
			<div id="data-selector">
				<form method="GET">
					<select name="datafile">
						<?php
							echo "<option>$datafile</option>"; 
							foreach($datafiles as $d) {
								echo "<option>$d</option>"; 
							}				
						?>					
					</select>

					<button>Change</button>
				</form>
			</div>
			
			
		</div>
	</div>
	
	
	<div id="previewarea">
		<div class="titlebar">
			<a href="#" id="action-dismiss">x</a>
			<strong>Search Results</strong>
			<a href="#" id="action-save">Accept</a>
		</div>
		<div id="preview-items-wrapper">
			<div id="preview-items">
			</div>
		</div>

	</div>

	
	
	<script type="text/javascript">
    	var cfg = {
    		startLocation: "<?php echo $country; ?>",
    		country: "<?php echo $country; ?>", 
    		datafile: "<?php echo $datafile; ?>"
    	}; 
    	<?php
    		$itm = @file_get_contents(DATA_FILE_DIR.$datafile); 
    		if($itm === false) {$itm="[]";}
    	?>
    	var existingItems=<?php echo stripslashes($itm);?>;
    	var items = []; 	
	</script>
</body>
</html>
