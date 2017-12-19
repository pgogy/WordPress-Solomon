var ActiveText = "";
var Overlay = "";

jQuery(document).ready(
	function(){

		if(mapData.latLng==""){
			x=0;
			y=0;
		}else{
			parts = mapData.latLng.split(" ");
			x = parts[0];
			y = parts[1];
		}
		
		if(mapData.zoom==""){
			zoom = 3;
		}else{
			zoom = mapData.zoom;
		}

		map = L.map('map').setView([x,y], zoom);
		
		if(mapData.src!=""){
			TL = jQuery("#overlaytl").val().split(",");	
			TR = jQuery("#overlaytr").val().split(",");	
			BR = jQuery("#overlaybr").val().split(",");	
			BL = jQuery("#overlaybl").val().split(",");	
				
			img = new L.DistortableImageOverlay(
						mapData.src, {
						corners: [	
										new L.latLng(parseFloat(TL[0]),parseFloat(TL[1])),
										new L.latLng(parseFloat(TR[0]),parseFloat(TR[1])),
										new L.latLng(parseFloat(BL[0]),parseFloat(BL[1])),
										new L.latLng(parseFloat(BR[0]),parseFloat(BR[1])),
								   ]
						}
					  ).addTo(map);
			
			L.DomEvent.on(img._image, 'load', img.editing.enable, img.editing);		
			
			img.on('edit', function(e) {
				jQuery("#overlaytl").val(e.target._corners[0]['lat'] + "," + e.target._corners[0]['lng']);
				jQuery("#overlaytr").val(e.target._corners[1]['lat'] + "," + e.target._corners[1]['lng']);
				jQuery("#overlaybl").val(e.target._corners[2]['lat'] + "," + e.target._corners[2]['lng']);
				jQuery("#overlaybr").val(e.target._corners[3]['lat'] + "," + e.target._corners[3]['lng']);	
			}, img);
		}
        
		mapLink =
            '<a href="http://openstreetmap.org">OpenStreetMap</a>';
        L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 22,
			id: 'examples.map'
        }).addTo(map);
		
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
								console.log("click");
								if(jQuery("#overlaytl").val()!=""&&jQuery("#overlaybr").val()!=""){
									console.log("click passed");
									TL = jQuery("#overlaytl").val().split(",");	
									TR = jQuery("#overlaytr").val().split(",");	
									BR = jQuery("#overlaybr").val().split(",");	
									BL = jQuery("#overlaybl").val().split(",");	
										
									img = new L.DistortableImageOverlay(
												jQuery(this).attr("src"), {
												  corners: [	
																new L.latLng(parseFloat(TL[0]),parseFloat(TL[1])),
																new L.latLng(parseFloat(TR[0]),parseFloat(TR[1])),
																new L.latLng(parseFloat(BL[0]),parseFloat(BL[1])),
																new L.latLng(parseFloat(BR[0]),parseFloat(BR[1])),
														   ]
												}
											  ).addTo(map);
									
									L.DomEvent.on(img._image, 'load', img.editing.enable, img.editing);		
									
									img.on('edit', function(e) {
										jQuery("#overlaytl").val(e.target._corners[0]['lat'] + "," + e.target._corners[0]['lng']);
										jQuery("#overlaytr").val(e.target._corners[1]['lat'] + "," + e.target._corners[1]['lng']);
										jQuery("#overlaybl").val(e.target._corners[2]['lat'] + "," + e.target._corners[2]['lng']);
										jQuery("#overlaybr").val(e.target._corners[3]['lat'] + "," + e.target._corners[3]['lng']);	
									}, img);
									
									/*var imageBounds = L.latLngBounds(
										[
											poly1[0],
											poly1[2]
										]
									);
									map.fitBounds(imageBounds);
									var overlay = new L.ImageOverlay(
										jQuery(this).attr("src"), 
										imageBounds, 
										{
											opacity: 0.7,
											interactive: true
										}
									);
									map.addLayer(overlay);
									
									var _image_edited = false;
									var draggable = new L.Draggable(overlay._image);

									L.DomEvent.on(overlay._image, 'dblclick', function(e) {
										e.preventDefault();
										_image_edited = !_image_edited;

										if(_image_edited)
											draggable.enable();
										else
											draggable.disable();
									});

									draggable.on('dragend', 
										function(e){
											console.log(e.target._newPos);
										}
									);*/
									
									/*
									var	bounds = new L.LatLngBounds(
											new L.LatLng(parseFloat(TL[0]),parseFloat(TL[1])),
											new L.LatLng(parseFloat(BR[0]),parseFloat(BR[1]))
										);
									map.fitBounds(bounds);
									var overlay = new L.ImageOverlay(jQuery(this).attr("src"), bounds, {
										opacity: 0.5,
										interactive: true,
									});
									m = map.addLayer(overlay);
									console.log(m);
									*/
								}
							}
						);
				}
			);

	}
);