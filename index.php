<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
        <script src="json2.js"></script>
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
	<style type="text/css">
	body, html, .maxheight {height: 100%; }
	#maps-area{height: 100%; }
	.new-item input { width: 90%; }
	.new-item select {width: 100%; }
	</style>
  </head>
  <body>
    
	<div class=" container-fluid maxheight"  >
		<div class="row-fluid maxheight" >
	    <div class="span9" id="maps-area">
	    	asasdf
	    </div>
		<div class="span3" id="markers-area">

		   <div class="navbar navbar-inverse">
      			<div class="navbar-inner">
					<div class="container">
						<a class="brand" href="#">Travellor</a>
						<div class="nav-collapse collapse">
							<ul class="nav">
								<li class="active"><a href="#" id="action-save">save</a></li>
							</ul>
      					</div><!--/.nav-collapse -->
    				</div>
				</div>
			</div>



			<form class="new-item">
				
				<input type="text" id="new-item-title" placeholder="Title" /><br/>
				<input type="text" id="new-item-address" placeholder="Address" /><br/>
				<select id="new-item-color">
					<option>Blue</option>
					<option>Green</option>
					<option>Red</option>
					<option>Yellow</option>
				</select>
				<p>
					<button type="button" id="new-item-preview" class="btn btn-primary">Search</button>
				</p>
				
				<p id="new-item-confirm-alert" class="alert fade in">
					Is this the location you are looking for? <br/>
					<button type="button" id="preview-confirm">Yes</button>
					<button type="button" id="preview-cancel">No</button>
				</p>
			</form>

			<table class="table table-striped">
				<thead>
					<tr>
						<th>Ico</th>
						<th>Address</th>
					</tr>
				</thead>
				<tbody id="data-table-body">
					
				</tbody>
			</table>
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
    	
		$(document).ready(function(){
			initMaps(); 
			initUi(); 



		}); 
		
		function initUi() {
			$("#new-item-preview").click(function(){
				var loc = $("#new-item-address").val();
				var title = $("#new-item-title").val();
				var color = $("#new-item-color").val();
				
				resolve(loc, function(data){
					centerOn(data);
					var previewMarker = marker.blank(data, color); 
					previewMarker.setDraggable(true); 
					$("#preview-confirm, #preview-cancel").unbind();
					$("#new-item-confirm-alert").show();
					$("#preview-confirm").click(function(){
						var confirmedLat  = previewMarker.getPosition(); 
						data.lat = confirmedLat.lat(); 
						data.lng = confirmedLat.lng(); 
						
						var item = new MapItem(title, loc, data.lat, data.lng, color); 
						item.setMarker(marker.nextOf(data, color)); 
						
						items.push(item);
						addToTable(item);
						previewMarker.setMap(null);						
						$("#new-item-confirm-alert").hide(); 
					});
					$("#preview-cancel").click(function(){
						previewMarker.setMap(null);
						$("#new-item-confirm-alert").hide(); 
					});
				}); 
			}); 
			$("#new-item-confirm-alert").hide(); 
			$("#action-save").click(function(e){ 
				var converted = []; 
				$.each(items, function(i, itm){converted.push(itm.export())});
				e.preventDefault(); 
				var d = {"content": JSON.stringify(converted) }; 
				$.ajax("datastore.php",{data: d, dataType: "json", type:"POST"})
					.done(function(){alert("Saved"); })
					.fail(function(){alert("error"); }); 
			});
			
			// init existing items
			$.each(existingItems, function(index, itm){
				var loaded = new MapItem(itm.title, itm.address, itm.lat, itm.lng, itm.color); 
				var m = marker.nextOf(itm, itm.color); 
				loaded.setMarker(m); 
				items.push(loaded); 
				addToTable(loaded); 

			}); 
		}
		
		function addToTable(mapItem) {
			var img = mapItem.marker.icon.url; 
			var imgSrc = "<img src='"+img+"' />"; 
			var html = "<tr><td>"+imgSrc+"</td><td><strong>"+mapItem.title+"</strong><br/>"+mapItem.address+"</td></tr>"; 
			var item  = $(html);
			console.log(item);
			item.mouseover(function(){mapItem.marker.setAnimation(google.maps.Animation.BOUNCE);}); 
			item.mouseout(function(){mapItem.marker.setAnimation(null);}); 
			$("#data-table-body").append(item); 
			console.log(mapItem);
		}
		
		function MapItem(title, address, lat, lng, color) { 
			this.title = title; 
			this.address = address; 
			this.lat = lat; 
			this.lng = lng; 
			this.color = color; 
			this.setMarker = function(marker) {this.marker = marker; }
			this.export = function() {
				return {
					title: this.title, 
					address: this.address, 
					lat: this.lat,
					lng: this.lng, 
					color: this.color
				}
			}
		}
		
		function initMaps() {

			var initialOpts = {
				zoom: 11,

				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			cfg.maps = new google.maps.Map(document.getElementById("maps-area"), initialOpts);
 			cfg.geocoder = new google.maps.Geocoder(); 
			
 			resolve(cfg.startLocation, centerOn); 
  
		}
		
		function addMarker(data) {
			var ll = new google.maps.LatLng(data.lat, data.lng);
			var mrk = new google.maps.Marker({position: ll, map: cfg.maps, title: data.search});
			return mrk; 
		}
		
		function centerOn(data) {
			var loc  = new google.maps.LatLng(data.lat, data.lng);
			cfg.maps.setCenter(loc);
		}
		
		function resolve(address, callback) {
			cfg.geocoder.geocode( { 'address': address+", "+cfg.country}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					var res = results[0].geometry.location; 
					callback({ "lat": res.lat(), "lng": res.lng(), "search":  address}); 
				} else {
					alert("Could not resolve "+address); 
				}
				
			}); 

					
		}
		
		var marker = {
			blank: function(data, color) {
				return marker.base(data, 'markers/largeTD'+color+"Icons/blank.png"); 	            
			}, 
			
			nextOf: function(data, color) {
				if(!marker.colorCounters[color]) { 
					marker.colorCounters[color]=1; 
				} else { 
					marker.colorCounters[color]++
				}
				var c  = marker.colorCounters[color];
				return marker.base(data, 'markers/largeTD'+color+"Icons/marker"+c+".png"); 
				
			}, 
			
			base: function(data, icon) {
				var size = new google.maps.Size(20, 34); 
    	        // The origin for this image is 0,0.
				var origin = new google.maps.Point(0,0);
				// The anchor for this image is the base of the flagpole at 0,32.
				var anchor = new google.maps.Point(10, 34);
				
				var image = new google.maps.MarkerImage(icon, size, origin, anchor); 
				
				var ll = new google.maps.LatLng(data.lat, data.lng);
				var marker = new google.maps.Marker({position: ll, map: cfg.maps, title: data.search, icon: image});
				return marker; 

			}, 
			
			colorCounters : {}
			
			
			
		}; 
    	
    	
    </script>
  </body>
</html>
