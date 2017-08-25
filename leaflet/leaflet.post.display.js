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
																	
																		console.log(jQuery("#map").position());
																		console.log(jQuery("#map").offset());
																	
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