jQuery(document).ready(
	function(){

		map = L.map('map').setView(mapData.latLng.split(" "), mapData.zoom);
        
		mapLink =
            '<a href="http://openstreetmap.org">OpenStreetMap</a>';
        L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 22,
			id: 'examples.map'
        }).addTo(map);
	
	}
);