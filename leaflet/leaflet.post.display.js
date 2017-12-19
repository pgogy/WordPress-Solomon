function display_image(id){
	
	jQuery.ajax({
			url : leaflet_add_to_map.ajax_url,
			type : 'post',
			data : {
				action : 'get_post_geo_image',
				post_id : id,
			},
			success : function( response ) {
				
					data = JSON.parse(response);
					
					console.log(response);
			
					anchors = data['anchors'].split(",");	
					markers = data['markers'].split(",");	
					
					anchorPoints = Array();
					markerPoints = Array();
					
					for(x in anchors){
						if(anchors[x].split(" ").join("")!=""){
							anchorPoint = anchors[x].split(" ");
							if(!isNaN(anchorPoint[0])){
								anchorPoints.push(Array(parseFloat(anchorPoint[0]),parseFloat(anchorPoint[1])));
							}
						}
					}
					
					for(x in markers){
						if(markers[x].split(" ").join("")!=""){
							markerPoint = markers[x].split(" ");
							if(!isNaN(markerPoint[0])){
								markerPoints.push(Array(parseFloat(markerPoint[0]),parseFloat(markerPoint[1])));
							}
						}
					}
					
					var anchors = anchorPoints;
					var clipCoords = markerPoints;
					var image = L.imageTransform(data['src'], anchorPoints, { opacity: 1, clip: clipCoords, disableSetClip: false }).addTo(map);
					
				}
			}
		);

}



function display_ioverlay(id){
	
	jQuery.ajax({
				url : leaflet_add_to_map.ajax_url,
				type : 'post',
				data : {
					action : 'get_post_geo_imagedistortedoverlay',
					post_id : id,
				},
				success : function( response ) {
					
					data = JSON.parse(response);
			
					console.log(data);

					TL = data['tl'].split(",");	
					TR = data['tr'].split(",");	
					BR = data['br'].split(",");	
					BL = data['bl'].split(",");	
						
					img = new L.DistortableImageOverlay(
								data['src'], {
								corners: [	
												new L.latLng(parseFloat(TL[0]),parseFloat(TL[1])),
												new L.latLng(parseFloat(TR[0]),parseFloat(TR[1])),
												new L.latLng(parseFloat(BL[0]),parseFloat(BL[1])),
												new L.latLng(parseFloat(BR[0]),parseFloat(BR[1])),
										   ]
								}
							  ).addTo(map);
							  
					jQuery("#map img[src='" + data['src'] + "']")
						.first()
						.attr("id",id);
							  
					img.on('click', function(e) {
												console.log("click");
												console.log(e);
												if(e.originalEvent.srcElement.getAttribute("id").indexOf("mapitem_")!=-1){
													id = e.originalEvent.srcElement.getAttribute("id");
													console.log(id);
													jQuery.ajax({
														url : leaflet_add_to_map.ajax_url,
														type : 'post',
														data : {
															action : 'get_post_content',
															post_id : id,
														},
														success : function( response ) {
														
															console.log("response is " + response);
														
															console.log(jQuery("#map").position());
															console.log(jQuery("#map").offset());
														
															if(jQuery("#mapInfo").length==0){
															
																jQuery("body")
																	.append("<div id='mapInfo' class='" + e.originalEvent.srcElement.getAttribute("id") + "'>" + response + "</div>");		
																		
																jQuery("#mapInfo")	
																	.css("position","absolute")
																	.css("z-index","1000000")
																	.css("top",(jQuery("#map").height()/2) + jQuery("#map").offset().top )
																	.css("left", jQuery("#map").offset().left);
																	
															}else{
															
																jQuery("#mapInfo").html(response);
															
															}
														}
													});
												}	  
											});		  
				
				}
				
			}
		
		);
	
}

function display_item(id){
	
	jQuery.ajax({
		url : leaflet_add_to_map.ajax_url,
		type : 'post',
		data : {
			action : 'get_post_geo',
			post_id : id,
		},
		success : function( response ) {
			
			data = JSON.parse(response);
		
			for(x in data["geo"]){
			
				className = id;
			
				if(data["geo"][x].split(" ").join("").length!=0){
				
					parts = data["geo"][x].split("=");
				
					if(parts[0]=="POLYGON"){
						
						co_ords = parts[1].split(",");
						
						co_ords_holder = Array();
						
						for(y=0;y<=co_ords.length;y+=2){
							if(co_ords[y+1]!=undefined){
								pointX = co_ords[y];
								pointY = co_ords[y+1];
								co_ords_holder.push(new L.LatLng(pointX,pointY));
							}
						}
							
						var m = new L.Polygon(co_ords_holder);
		
						m.options['color'] = (data['strokecolors']) ? data['strokecolors'][x-1] : ""; 
						m.options['stroke-opacity'] = (data["strokeopacity"]) ? data["strokeopacity"][x-1] : "";
						m.options['fillColor'] = (data['colors']) ? data['colors'][x-1] : "";
						m.options['fillOpacity'] = (data["opacity"]) ? data["opacity"][x-1] : "";
						m.options['dashArray'] = (data["strokes"]) ? data["strokes"][x-1].split("--").join(" ") : "";
						m.options['className'] = className;
											
						map.addLayer(m);
					
					}
					
					if(parts[0] == "POLYLINE")
					{
					
						co_ords = parts[1].split(",");
						
						co_ords_holder = Array();
						
						for(y=0;y<=co_ords.length;y+=2){
							if(co_ords[y+1]!=undefined){
								pointX = co_ords[y];
								pointY = co_ords[y+1];
								co_ords_holder.push(new L.LatLng(pointX,pointY));
							}
						}
							
						var m = new L.Polyline(co_ords_holder);
						
						m.options['color'] = (data['strokecolors']) ? data['strokecolors'][x-1] : ""; 
						m.options['stroke-opacity'] = (data["strokeopacity"]) ? data["strokeopacity"][x-1] : "";
						m.options['fillColor'] = (data['colors']) ? data['colors'][x-1] : "";
						m.options['fillOpacity'] = (data["opacity"]) ? data["opacity"][x-1] : "";
						m.options['dashArray'] = (data["strokes"]) ? data["strokes"][x-1].split("--").join(" ") : "";
						m.options['className'] = className;
						
						map.addLayer(m);
						
					}
					
					if(parts[0] == "CIRCLE")
					{

						co_ords = parts[1].split(",");

						var m = new L.circle(new L.LatLng(co_ords[0],co_ords[1]),parts[2]);
						m.options['color'] = (data['strokecolors']) ? data['strokecolors'][x-1] : ""; 
						m.options['stroke-opacity'] = (data["strokeopacity"]) ? data["strokeopacity"][x-1] : "";
						m.options['fillColor'] = (data['colors']) ? data['colors'][x-1] : "";
						m.options['fillOpacity'] = (data["opacity"]) ? data["opacity"][x-1] : "";
						m.options['dashArray'] = (data["strokes"]) ? data["strokes"][x-1].split("--").join(" ") : "";
						m.options['className'] = className;
						
						map.addLayer(m);
				
					}
					
					if(parts[0]=="POINT"){
						
						size = data['markersizes'][x-1].split(",");
						anchor = data['markersizes'][x-1].split(",");
						
						anchor[0] = anchor[0] / 2; 
						anchor[1] = anchor[1] / 2; 
						
						customIcon = L.icon({
							iconUrl: data['markers'][x-1],
							shadowUrl: "",
							iconSize: size, 
							iconAnchor: anchor
						});

						co_ords = parts[1].split(",");

						var m = new L.marker(new L.LatLng(co_ords[0],co_ords[1]),{icon: customIcon});
						m.options['className'] = className;
						m.options.icon.options.className = className;
						map.addLayer(m);
						
						
					}
					
					m.on('click', function(e) { 
												for(x in e.originalEvent.srcElement.classList){
													if(typeof e.originalEvent.srcElement.classList[x] === "string"){
														if(e.originalEvent.srcElement.classList[x].indexOf("mapitem_")!=-1){
															if(e.originalEvent.srcElement.classList[x].indexOf(" ")==-1){
																id = e.originalEvent.srcElement.classList[x].split("_").pop();
																jQuery.ajax({
																	url : leaflet_add_to_map.ajax_url,
																	type : 'post',
																	data : {
																		action : 'get_post_content',
																		post_id : id,
																	},
																	success : function( response ) {
																	
																		if(jQuery("#mapInfo").length==0){
																		
																			jQuery("body")
																				.append("<div id='mapInfo' class='" + e.originalEvent.srcElement.classList[x] + "'>" + response + "</div>");		
																					
																			jQuery("#mapInfo")	
																				.css("position","absolute")
																				.css("z-index","1000000")
																				.css("top",(jQuery("#map").height()/2) + jQuery("#map").offset().top )
																				.css("left", jQuery("#map").offset().left);
																				
																		}else{
																		
																			jQuery("#mapInfo").html(response);
																		
																		}
																	}
																});
																
															}
														}
													}				
												}	  
											});
					
				}
				
			}
			
		}
	})
}

function display_item_label(id){
	
	jQuery.ajax({
		url : leaflet_add_to_map.ajax_url,
		type : 'post',
		data : {
			action : 'get_post_geo',
			classes : "mapitemlabel",
			post_id : id,
		},
		success : function( response ) {
			
			data = JSON.parse(response);
		
			for(x in data["geo"]){
			
				className = id;
			
				if(data["geo"][x].split(" ").join("").length!=0){
				
					parts = data["geo"][x].split("=");
				
					if(parts[0]=="POLYGON"){
						
						co_ords = parts[1].split(",");
						
						co_ords_holder = Array();
						
						for(y=0;y<=co_ords.length;y+=2){
							if(co_ords[y+1]!=undefined){
								pointX = co_ords[y];
								pointY = co_ords[y+1];
								co_ords_holder.push(new L.LatLng(pointX,pointY));
							}
						}
							
						var m = new L.Polygon(co_ords_holder).bindLabel(data['label'], { noHide: true });
		
						m.options['color'] = (data['strokecolors']) ? data['strokecolors'][x-1] : ""; 
						m.options['stroke-opacity'] = (data["strokeopacity"]) ? data["strokeopacity"][x-1] : "";
						m.options['fillColor'] = (data['colors']) ? data['colors'][x-1] : "";
						m.options['fillOpacity'] = (data["opacity"]) ? data["opacity"][x-1] : "";
						m.options['dashArray'] = (data["strokes"]) ? data["strokes"][x-1].split("--").join(" ") : "";
						m.options['className'] = className;
											
						map.addLayer(m);
					
					}
					
					if(parts[0] == "POLYLINE")
					{
					
						co_ords = parts[1].split(",");
						
						co_ords_holder = Array();
						
						for(y=0;y<=co_ords.length;y+=2){
							if(co_ords[y+1]!=undefined){
								pointX = co_ords[y];
								pointY = co_ords[y+1];
								co_ords_holder.push(new L.LatLng(pointX,pointY));
							}
						}
							
						var m = new L.Polyline(co_ords_holder).bindLabel(data['label'], { noHide: true });
						
						m.options['color'] = (data['strokecolors']) ? data['strokecolors'][x-1] : ""; 
						m.options['stroke-opacity'] = (data["strokeopacity"]) ? data["strokeopacity"][x-1] : "";
						m.options['fillColor'] = (data['colors']) ? data['colors'][x-1] : "";
						m.options['fillOpacity'] = (data["opacity"]) ? data["opacity"][x-1] : "";
						m.options['dashArray'] = (data["strokes"]) ? data["strokes"][x-1].split("--").join(" ") : "";
						m.options['className'] = className;
						
						map.addLayer(m);
						
					}
					
					if(parts[0] == "CIRCLE")
					{

						co_ords = parts[1].split(",");

						var m = new L.circle(new L.LatLng(co_ords[0],co_ords[1]),parts[2]).bindLabel(data['label'], { noHide: true });
						m.options['color'] = (data['strokecolors']) ? data['strokecolors'][x-1] : ""; 
						m.options['stroke-opacity'] = (data["strokeopacity"]) ? data["strokeopacity"][x-1] : "";
						m.options['fillColor'] = (data['colors']) ? data['colors'][x-1] : "";
						m.options['fillOpacity'] = (data["opacity"]) ? data["opacity"][x-1] : "";
						m.options['dashArray'] = (data["strokes"]) ? data["strokes"][x-1].split("--").join(" ") : "";
						m.options['className'] = className;
						
						map.addLayer(m);
				
					}
					
					if(parts[0]=="POINT"){
						
						size = data['markersizes'][x-1].split(",");
						anchor = data['markersizes'][x-1].split(",");
						
						anchor[0] = anchor[0] / 2; 
						anchor[1] = anchor[1] / 2; 
						
						customIcon = L.icon({
							iconUrl: data['markers'][x-1],
							shadowUrl: "",
							iconSize: size, 
							iconAnchor: anchor
						});

						co_ords = parts[1].split(",");

						var m = new L.marker(new L.LatLng(co_ords[0],co_ords[1]),{icon: customIcon}).bindLabel(data['label'], { noHide: true });
						m.options['className'] = className;
						m.options.icon.options.className = className;
						map.addLayer(m);
						
						
					}
					
				}
				
			}
			
		}
	})
}


function display_item_text_label(id,defaultURL){
	
	jQuery.ajax({
		url : leaflet_add_to_map.ajax_url,
		type : 'post',
		data : {
			action : 'get_post_geo',
			classes : "mapitemlabel",
			post_id : id,
		},
		success : function( response ) {
			
			data = JSON.parse(response);
		
			for(x in data["geo"]){
			
				className = id;
			
				if(data["geo"][x].split(" ").join("").length!=0){
				
					parts = data["geo"][x].split("=");
				
					if(parts[0]=="POINT"){
						
						size = Array(0,0);
						anchor = data['markersizes'][x-1].split(",");
						
						anchor[0] = anchor[0] / 2; 
						anchor[1] = anchor[1] / 2; 
						
						customIcon = L.icon({
							iconUrl: defaultURL,
							shadowUrl: "",
							iconSize: size, 
							iconAnchor: anchor
						});

						co_ords = parts[1].split(",");

						var m = new L.marker(new L.LatLng(co_ords[0],co_ords[1]),{icon: customIcon}).bindLabel(data['label'], { noHide: true });
						map.addLayer(m);
						
						
					}
					
				}
				
			}
			
		}
	})
}

