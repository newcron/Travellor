<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="json2.js"></script>
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
    <script type="text/javascript" src="travellor.js"></script>
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>
  <body>
    
	    <div id="maps-area">

	    </div>
		<div  id="markers-area">
			<div id="titlebar">
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
			</div>
    	</div>
    	</div>
	</div>
	    <script type="text/javascript">
    	var cfg = {
    		startLocation: "Hongkong",
    		country: "HK"
    	}; 
    	<?php
    		$itm = @file_get_contents("datafile.json"); 
    		if($itm === false) {$itm="[]";}
    	?>
    	var existingItems=<?php echo stripslashes($itm);?>;
    	var items = []; 
    	
	
    	
    	
    </script>
  </body>
</html>
