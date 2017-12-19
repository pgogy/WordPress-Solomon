var ActiveText = "";
var Overlay = "";

jQuery(document).ready(
	function(){
	
		console.log(mapData);

		if(mapData.latLng==""){
			x=0;
			y=0;
		}else{
			parts = mapData.latLng.split(" ");
			x = parseFloat(parts[0]);
			y = parseFloat(parts[1]);
		}
		
		if(mapData.zoom==""){
			zoom = 3;
		}else{
			zoom = mapData.zoom;
		}

		map = L.map('map').setView([x,y], zoom);
		
		mapLink =
            '<a href="http://openstreetmap.org">OpenStreetMap</a>';
        L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 22,
			id: 'examples.map'
        }).addTo(map);
		
		if(mapData.src!=""){
		
			console.log("picture");
		
			TL = jQuery("#overlaytl").val().split(",");	
			BR = jQuery("#overlaybr").val().split(",");	
			anchors = jQuery("#overlayanchors").val().split(",");	
			markers = jQuery("#overlaymarkers").val().split(",");	

			anchorPoints = Array();
			markerPoints = Array();
			
			for(x=0;x<=anchors.length;x+=2){
				if(anchors[x]!=undefined&&anchors[x+1]!=undefined){
					if(!isNaN(anchors[x])){
						anchorPoints.push(Array(parseFloat(anchors[x]),parseFloat(anchors[x+1])));
					}
				}
			}

			for(x=0;x<=markers.length;x+=2){
				console.log(x + " " + markers[x] + " " +  markers[x+1]);
				if(markers[x]!=undefined&&markers[x+1]!=undefined){
					if(!isNaN(markers[x])){
						markerPoints.push(Array(parseFloat(markers[x]),parseFloat(markers[x+1])));
					}
				}
			}

			console.log(anchorPoints);
			console.log(markerPoints);

			var anchors = anchorPoints;
			var clipCoords = markerPoints;
			var image = L.imageTransform(mapData.src, anchorPoints, { opacity: 1, clip: clipCoords, disableSetClip: false }).addTo(map);
			var externalPolygon = L.polygon(anchorPoints, {fill: false}).addTo(map);
			
			var clipPolygon = L.polygon(clipCoords, {fill: false, color: 'red'}).addTo(map);
			if (!image.options.disableSetClip) {
				clipPolygon.editing.enable();
				
				clipPolygon.on('edit', function() {
					image.setClip(clipPolygon.getLatLngs());
					
					data = clipPolygon.getLatLngs();
					
					markers = "";
					
					for(x in data){
						markers += data[x]['lat'] + " " + data[x]['lng'] + ",";
					}
					
					jQuery("#overlaymarkers").val(markers);
					
				});
			}
			
			var updateAnchors = function() {
				
				var anchors = anchorMarkers.map(function(marker){ return marker.getLatLng(); })
				
				image.setAnchors(anchors);
				externalPolygon.setLatLngs(anchors);
				clipPolygon.setLatLngs(image.getClip());
				
				if (!image.options.disableSetClip) {
					clipPolygon.editing.disable();
					clipPolygon.editing.enable();
				}
				
				markers = "";
				
				for(x in map._layers){
					classNames = map._layers[x]['_icon'];
					if(classNames!=undefined){
						node = classNames.toString();
						if(node.indexOf("Image")!=-1){
							markers += map._layers[x]._latlng['lat'] + " " + map._layers[x]._latlng['lng'] + ",";
						}
					}
				}
				
				jQuery("#overlayanchors").val(markers);
				
			}
			
			var anchorMarkers = anchors.map(function(anchor) {
				return L.marker(anchor, {draggable: true}).addTo(map).on('drag', updateAnchors);
			})
			

		}
        
		
		map.on('move', function (e) {
				
			latLng = map.getCenter();
			if(latLng['lat']!=0){
				jQuery("#EntryMapCenter").val(latLng['lat'] + " " + latLng['lng']);
			}
			jQuery("#EntryMapZoom").val(e.target['_zoom']);
			
		});
		
		map.on('click', function(e) {
			if(jQuery(e.originalEvent.target).attr("class").indexOf("image-layer")!=-1){
				
			}else{
				jQuery(ActiveText).val(e.latlng.lat + "," + e.latlng.lng);
			}
		});	
		
		jQuery(".overlayholder")
			.each(
				function(index,value){
					jQuery(value)
						.on("click", function(){
								jQuery(this)
									.css("border","2px solid #00F");
								jQuery(ActiveText)
									.css("border","none");
								ActiveText = jQuery(this);
							}
						);
				}
			);
			
		jQuery(".overlayholder")
			.first()
			.trigger("click");
			
		jQuery("#overlayimages img")
			.each(
				function(index,value){
					jQuery(value)
						.on("click", function(){
								
								jQuery("#imageoverlay").val(jQuery(this).attr("src"))
								
								if(jQuery("#overlaytl").val()!=""&&jQuery("#overlaybr").val()!=""){
									
									TL = jQuery("#overlaytl").val().split(",");	
									BR = jQuery("#overlaybr").val().split(",");	
									
									var anchors = [[parseFloat(TL[0]), parseFloat(TL[1])], [parseFloat(TL[0]), parseFloat(BR[1])], [parseFloat(BR[0]), parseFloat(BR[1])], [parseFloat(BR[0]), parseFloat(TL[1])]];

									jQuery("#overlayanchors").val(anchors);

									var clipCoords = [[parseFloat(TL[0]), parseFloat(TL[1])], [parseFloat(TL[0]), parseFloat(BR[1])], [parseFloat(BR[0]), parseFloat(BR[1])], [parseFloat(BR[0]), parseFloat(TL[1])]];

									jQuery("#overlaymarkers").val(clipCoords);

									var image = L.imageTransform(jQuery(this).attr("src"), anchors, { opacity: 1, clip: clipCoords, disableSetClip: false }).addTo(map);

									var externalPolygon = L.polygon(anchors, {fill: false}).addTo(map);
									
									var clipPolygon = L.polygon(clipCoords, {fill: false, color: 'red'}).addTo(map);
									
									if (!image.options.disableSetClip) {
										clipPolygon.editing.enable();
										
										clipPolygon.on('edit', function() {
											image.setClip(clipPolygon.getLatLngs());
											
											data = clipPolygon.getLatLngs();
											
											markers = "";
											
											for(x in data){
												markers += data[x]['lat'] + " " + data[x]['lng'] + ",";
											}
											
											jQuery("#overlaymarkers").val(markers);
											
										});
									}
									
									var updateAnchors = function() {
										
										var anchors = anchorMarkers.map(function(marker){ return marker.getLatLng(); })
										
										image.setAnchors(anchors);
										externalPolygon.setLatLngs(anchors);
										clipPolygon.setLatLngs(image.getClip());
										
										if (!image.options.disableSetClip) {
											clipPolygon.editing.disable();
											clipPolygon.editing.enable();
										}
										
										markers = "";
										
										for(x in map._layers){
											classNames = map._layers[x]['_icon'];
											if(classNames!=undefined){
												node = classNames.toString();
												if(node.indexOf("Image")!=-1){
													markers += map._layers[x]._latlng['lat'] + " " + map._layers[x]._latlng['lng'] + ",";
												}
											}
										}
										
										jQuery("#overlayanchors").val(markers);
										
									}
									
									var anchorMarkers = anchors.map(function(anchor) {
										return L.marker(anchor, {draggable: true}).addTo(map).on('drag', updateAnchors);
									})
									
								}
							}
						);
				}
			);

	}
);