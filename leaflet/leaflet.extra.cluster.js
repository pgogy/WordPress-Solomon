var map = "";
var drawnItems = "";
var drawControl = "";
var current = "";

jQuery(document).ready(
	function(){
	
		jQuery("#geocustommarkers")
			.children()
			.first()
			.trigger("click");

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

		drawnItems = new L.FeatureGroup();

		var customIcon = L.icon({
					iconUrl: "http://localhost/2.jpg",
					iconSize:     [25, 41], // size of the icon
					iconAnchor:   [0, 20], // point of the icon which will correspond to marker's location
		});	

		//configuring what shapes users can draw
		drawControl = new L.Control.Draw({
						position: 'topright',
						draw: {
								marker: {
										icon: customIcon,
								}
						},
						edit: {
							featureGroup: drawnItems,
							remove: true
						}
				});

		L.DrawToolbar.include({
			getModeHandlers: function (map) {
				return [
					{
						enabled: this.options.marker,
						handler: new L.Draw.Marker(map, this.options.marker),
						title: L.drawLocal.draw.toolbar.buttons.marker
					}
				];
			}
		});
	
		new L.Control.GeoCustomMarker().addTo(map);

		map.addControl(drawControl);
		
		map.addLayer(drawnItems);


		new L.Control.GeoSearch({
			provider: new L.GeoSearch.Provider.OpenStreetMap(),
			crossOrigin: null,
			showMarker: false
		}).addTo(map);
		
		map.on('move', function (e) {
				
			latLng = map.getCenter();
			if(latLng['lat']!=0){
				jQuery("#EntryMapCenter").val(latLng['lat'] + " " + latLng['lng']);
			}
			jQuery("#EntryMapZoom").val(e.target['_zoom']);
			
		});
		
		map.on('draw:drawstart', function (e) {
			if(e.layerType.toString()=="marker"){
				if(jQuery("#custom_" + L.GeoCustomMarker.defaultIcon).length!=0){
					size = jQuery("#custom_" + L.GeoCustomMarker.defaultIcon).attr("size").split(",");
				}
				drawControl.options.draw.marker.icon.options.iconUrl = L.GeoCustomMarker.defaultMarker;
				drawControl.options.draw.marker.icon.options.iconSize = size;
				drawControl.options.draw.marker.icon.options.iconAnchor = [(size[0]/2), (size[1])]
			}
		});
		
		//creating a new point event
		map.on('draw:created', function (e) {
		
			if(jQuery("#custom_" + L.GeoCustomMarker.defaultIcon).length!=0){
				
				size = jQuery("#custom_" + L.GeoCustomMarker.defaultIcon).attr("size").split(",");
						
				customIcon = L.icon({
					iconUrl: jQuery("#custom_" + L.GeoCustomMarker.defaultIcon).attr("iconurl"),
					iconSize: size,
				});
				
				var type = e.layerType,
				layer = e.layer;
				drawnItems.addLayer(layer);
				current = "";
			
			}
		
			var shapes = getShapes(drawnItems, e['layerType']);
			
			jQuery('#EntryLatlng').val(shapes);
			
			processShapes();
			
		});
			
		map.on('draw:edited', function (e) {
		
			var layers = e.layers;
			layers.eachLayer(function (layer) {
				//pick new coordinate after edit
				var shapes = getShapes(drawnItems);
				jQuery('#EntryLatlng').val(shapes, e['layerType']);
			});
			
			processShapes();
		
		});
		
		//delete event
		map.on('draw:deleted', function () {
			var shapes = getShapes(drawnItems, null);
			//picking coordinates after delete if any
			jQuery('#EntryLatlng').val(shapes);
			//enable the save map data button
			
			processShapes();
			
		});
		
		var processShapes = function(){
			var markerURLs = [];
			var markerSizes = [];
			counter = 0;

			drawnItems.eachLayer(function(layer) {
				if(layer.options.icon!=undefined){
					markerURLs.push(layer._icon.currentSrc);	
					markerSizes.push(layer._icon.clientWidth + "," + layer._icon.clientHeight);	
				}		
			});
			jQuery("#EntryMarkers").val(markerURLs.join(" "));
			jQuery("#EntryMarkerSizes").val(markerSizes.join(" "));
			
		}

		var getShapes = function(drawnItems, layerType) {
		
			var shapes = [];

			drawnItems.eachLayer(function(layer) {
				if (layer instanceof L.Marker) {
					shapes.push('geoPOINT='+[layer.getLatLng()]);
				}
			});
			
			return shapes;
		};
	
	}
);