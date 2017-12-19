function display_item(id,markers){
	
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
					
					if(parts[0]=="POINT"){
						
						size = data['markersizes'][x-1].split(",");
						anchor = data['markersizes'][x-1].split(",");
						
						anchor[0] = anchor[0] / 2; 
						anchor[1] = anchor[1] / 2; 
						
						customIcon = L.icon({
							iconUrl: data['markers'][x-1],
							iconSize: size
						});

						co_ords = parts[1].split(",");

						var m = new L.marker(new L.LatLng(co_ords[0],co_ords[1]),{icon: customIcon});
						m.options['className'] = className;
						m.options.icon.options.className = className;
						markers.addLayer(m);						
						
					}
					
				}
				
			}
			
		}
	})
}

