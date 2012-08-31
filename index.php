<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    
    <script src="json2.js"></script>
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
	<style type="text/css">
	body, html, .maxheight {height: 100%; padding: 0px; margin: 0px; font-family: Helvetica, Verdana, Arial }
	#maps-area{height: 100%; width: 100%;  }
	#markers-area {
		position: fixed; 
		top: 5px; 
		right: 5px; 
		
		width: 250px; 
		height: 400px; 
		box-shadow: 2px 2px 2px rgba(0,0,0,0.4); 
		background: white; 
		border-radius: 2px;
		border: 1px solid black;
	}
	
	#titlebar { background: black; padding: 10px; color: white; font-weight: normal; }
	#titlebar strong {display: inline-box; margin-right: 50px; }
	#titlebar a {color: white; text-decoration: none; text-align: right; color: #ccc; font-size: 0.8em; }
	#contentarea { padding: 10px; }
	.new-item input { width: 98%; margin-bottom: 6px; border: 1px solid #ccc; padding: 3px;}
	
	#new-item-confirm-alert { 
		border-color: black; 
		border-style: solid;
		border-width: 1px 0;
		padding: 3px 0; 
	} 
	
	#address-table {max-height: 300px; overflow: scroll-y; border-collapse: collapse; width: 100%;}
	#address-table-wrapper {height: 250px; overflow: scroll; border: 1px solid black; margin-top: 10px; }
	#data-table-body { max-height: 100px; overflow: hidden;}
	.table-icon-cell {width: 32px; height: 28px; overflow: hidden;} 
	#address-table td { border-bottom: 1px solid #ccc; padding: 2px; font-size: 0.8em; vertical-align: top;}
	#address-table td:first-child {width: 32px;}
	#address-table th { border: 1px solid black; padding: 2px; font-size: 0.8em; color: white; background: black;}
	
	#address-table .selected { background: #ccc; }

	</style>
  </head>
  <body>
    
	    <div id="maps-area">

	    </div>
		<div  id="markers-area">
			<div id="titlebar">
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
		
		var activeItem = null; 
		var activeMarker = null; 
		
		function addToTable(mapItem) {
			var img = mapItem.marker.icon.url; 

			var imgSrc = "<div class=\"table-icon-cell\"><img src='"+img+"' /></div>"; 
			var html = "<tr><td>"+imgSrc+"</td><td><strong>"+mapItem.title+"</strong><br/>"+mapItem.address+"</td></tr>"; 
			var $item  = $(html);
			
			
			$item.click(function(){
				if(activeItem!=null) {
					activeItem.removeClass("selected"); 
					activeMarker.setAnimation(null); 
				}
				$item.addClass("selected"); 
				activeMarker = mapItem.marker;
				activeMarker.setAnimation(google.maps.Animation.BOUNCE);
				
				activeItem = $item;
				console.log($item);
				$("#action-delete").detach(); 
				
				var $controls = $('<a href="#" id="action-delete">Delete</a>');

				$("#titlebar").append($controls); 
				$controls.click(function(){
				 
					$controls.detach(); 
					$item.detach(); 
					mapItem.marker.setMap(null); 
					items = $.grep(items, function(i){return i !== mapItem});
				}); 

			}); 
			
			
			$("#data-table-body").append($item); 
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
				var origin = new google.maps.Point(0,0);
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
