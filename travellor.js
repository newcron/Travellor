var travellor = {}; 
(function(){

	$(document).ready(function(){
		travellor.maps = initMaps(); 
		initUi(); 
		travellor.preview = initPreview(); 
	
/*		travellor.maps.geocoder.geocode( { 'address': "night market, hong kong"}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					travellor.preview.openPreviewDialog(results); 
				} else {
					alert("Could not resolve "+address); 
				}

			});  */
	
	}); 
	
	function initUi() {
		$("#action-hide").click(function(){
			$("#contentarea").slideToggle(); 
		});
		$("#new-item-preview").click(function(){
			var loc = $("#new-item-address").val();
			var title = $("#new-item-title").val();
			var color = $("#new-item-color").val();
			
			travellor.maps.resolve(loc, function(data){
				centerOnAndZoom(data);
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
			var d = {
				"content": JSON.stringify(converted), 
				"datafile": cfg.datafile
			}; 
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
	
	function initPreview() {
		$("#previewarea").hide(); 
		
		var openPreviewDialog = function(resultProposals) {
			$("#previewarea").show();
			var $pi = $("#preview-items"); 
			$pi.html(""); 

			$.each(resultProposals, function(ix, it){
				console.log(it); 
			});
		}
		
		return { openPreviewDialog: openPreviewDialog}
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
			centerOnAndZoom(mapItem);
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
		var maps = new google.maps.Map(document.getElementById("maps-area"), initialOpts);
		var geocoder = new google.maps.Geocoder(); 
		
		var resolve = function(address, callback) {
			geocoder.geocode( { 'address': address+", "+cfg.country}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					var res = results[0].geometry.location; 
					callback({ "lat": res.lat(), "lng": res.lng(), "search":  address}); 
				} else {
					alert("Could not resolve "+address); 
				}

			}); 
		}
		
		resolve(cfg.startLocation, centerOn); 
		
		return {
			maps: maps, 
			geocoder: geocoder, 
			resolve: resolve 
		}; 
	
	}
	
	
	function centerOn(data) {
		var loc  = new google.maps.LatLng(data.lat, data.lng);
		travellor.maps.maps.setCenter(loc);

	}
	
	function centerOnAndZoom(data) {
		centerOn(data); 
		travellor.maps.maps.setZoom(15); 
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
			var marker = new google.maps.Marker({position: ll, map: travellor.maps.maps, title: data.search, icon: image});
			return marker; 
	
		}, 
		
		colorCounters : {}
		
		
		
	}; 

})(); 