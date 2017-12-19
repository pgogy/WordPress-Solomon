var map = "";

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
        
		mapLink =
            '<a href="http://openstreetmap.org">OpenStreetMap</a>';
        L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 22,
			id: 'examples.map'
        }).addTo(map);
		
		console.log("pre markers");
		
	}
);