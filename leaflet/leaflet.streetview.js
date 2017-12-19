var map = "";
var drawnItems = "";
var drawControl = "";
var current = "";

jQuery(document).ready(
	function(){

		if(mapData.latLng==null||mapData.latLng==""){
			x=0;
			y=0;
		}else{
			parts = mapData.latLng.split(" ");
			x = parts[0];
			y = parts[1];
		}
		
		if(mapData.zoom==null||mapData.zoom==""){
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
		
		map.addLayer(drawnItems);

		new L.Control.GeoSearch({
			provider: new L.GeoSearch.Provider.OpenStreetMap(),
			crossOrigin: null,
			showMarker: false
		}).addTo(map);

		L.DrawToolbar.include({
			getModeHandlers: function (map) {
				return [
					{
						enabled: this.options.polyline,
						handler: new L.Draw.Polyline(map, {maxNodes:2}),
						title: L.drawLocal.draw.toolbar.buttons.polyline
					}
				];
			}
		});	

		//configuring what shapes users can draw
		drawControl = new L.Control.Draw({
						position: 'topright',
						draw: {
								Polyline: {
									metric: false,
									maxNodes: 2
								}
						},
						edit: {
							featureGroup: drawnItems,
							remove: true
						}
				});
				
			
		map.addControl(drawControl);
		
		map.on('move', function (e) {
				
			latLng = map.getCenter();
			if(latLng['lat']!=0){
				jQuery("#EntryMapCenter").val(latLng['lat'] + " " + latLng['lng']);
			}
			jQuery("#EntryMapZoom").val(e.target['_zoom']);
			
		});
		
		
		map.on('draw:created', function (e) {
		
			console.log("draw created");
			console.log(e);
		
			var type = e.layerType,
			layer = e.layer;
			drawnItems.addLayer(layer);
			
			var shapes = getShapes(drawnItems, e['layerType']);
			
			jQuery('#EntryLatlng').val(shapes);
			
		});
			
		map.on('draw:edited', function (e) {
		
			var layers = e.layers;
			layers.eachLayer(function (layer) {
				//pick new coordinate after edit
				var shapes = getShapes(drawnItems);
				jQuery('#EntryLatlng').val(shapes, e['layerType']);
			});
			
		});
		
		//delete event
		map.on('draw:deleted', function () {
			var shapes = getShapes(drawnItems, null);
			//picking coordinates after delete if any
			jQuery('#EntryLatlng').val(shapes);
			
		});

		var getShapes = function(drawnItems, layerType) {
		
			var shapes = [];

			drawnItems.eachLayer(function(layer) {
			
				if ((layer instanceof L.Polyline) && !(layer instanceof L.Polygon)) {
					shapes.push('geoPOLYLINE='+layer.getLatLngs());
				}
				
			});
			
			return shapes;
		};
		
	
	}
);