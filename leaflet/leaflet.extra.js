var map = "";
var drawnItems = "";
var drawControl = "";
var current = "";

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

		drawnItems = new L.FeatureGroup();
		
		map.addLayer(drawnItems);

		new L.Control.GeoLayerColor().addTo(map);
		new L.Control.GeoStrokeColor().addTo(map);
		new L.Control.GeoStrokeType().addTo(map);
		new L.Control.GeoCustomMarker().addTo(map);

		new L.Control.GeoSearch({
			provider: new L.GeoSearch.Provider.OpenStreetMap(),
			crossOrigin: null,
			showMarker: false
		}).addTo(map);

		var customIcon = L.icon({
					iconUrl: "http://localhost/2.jpg",
					shadowUrl: '',

					iconSize:     [25, 41], // size of the icon
					shadowSize:   [50, 64], // size of the shadow
					iconAnchor:   [12, 41], // point of the icon which will correspond to marker's location
					shadowAnchor: [4, 62],  // the same for the shadow
					popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
				});
				
		L.DrawToolbar.include({
			getModeHandlers: function (map) {
				return [
					{
						enabled: this.options.polyline,
						handler: new L.Draw.Polyline(map, this.options.polyline),
						title: L.drawLocal.draw.toolbar.buttons.polyline
					},
					{
						enabled: this.options.polygon,
						handler: new L.Draw.Polygon(map, this.options.polygon),
						title: L.drawLocal.draw.toolbar.buttons.polygon
					},
					{
						enabled: this.options.rectangle,
						handler: new L.Draw.Rectangle(map, this.options.rectangle),
						title: L.drawLocal.draw.toolbar.buttons.rectangle
					},
					{
						enabled: this.options.circle,
						handler: new L.Draw.Circle(map, this.options.circle),
						title: L.drawLocal.draw.toolbar.buttons.circle
					},
					{
						enabled: this.options.marker,
						handler: new L.Draw.Marker(map, this.options.marker),
						title: L.drawLocal.draw.toolbar.buttons.marker
					},
					{
						enabled: true,
						handler: new L.Draw.ImageOverlay(map, this.options.marker),
						title: 'Place Image Marker',
					},
				];
			}
		});	

		//configuring what shapes users can draw
		drawControl = new L.Control.Draw({
						position: 'topright',
						draw: {
								polyline: {
									metric: true,
								},
								polygon: {
										allowIntersection: true,
										showArea: true,
										drawError: {
												color: '#b00b00',
												timeout: 1000
										},
										shapeOptions: {
												color: '#000000'
										}
								},
								circle: {
										shapeOptions: {
												color: '#662d91'
										}
								},
								marker: {
										icon: customIcon,
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
		
		map.on('draw:drawstart', function (e) {
			if(e.layerType.toString()=="marker"){
				drawControl.options.draw.marker.icon.options.iconUrl = L.GeoCustomMarker.defaultMarker;
			}
		});
		
		//creating a new point event
		map.on('draw:created', function (e) {
		
			markerType = e.layerType.toString();
			
			if(markerType!="marker"){	
			
				console.log("***");
				console.log(map);
				console.log(map.fillColor);
			
				e.layer['options']['color'] = map.strokeColor;
				e.layer['options']['opacity'] = map.strokeOpacity;
				e.layer['options']['fillColor'] = map.drawColor;
				e.layer['options']['fillOpacity'] = map.drawOpacity;
				e.layer['options']['dashArray'] = map.strokeType;
		
				var type = e.layerType,
				layer = e.layer;
				drawnItems.addLayer(layer);
				
			}else{
			
				if(current!="ImageOverlay"){
			
					customIcon = L.icon({
						iconUrl: L.GeoCustomMarker.defaultMarker,
						shadowUrl: '',

						iconSize:     [25, 41], // size of the icon
						shadowSize:   [50, 64], // size of the shadow
						iconAnchor:   [12, 41], // point of the icon which will correspond to marker's location
						shadowAnchor: [4, 62],  // the same for the shadow
						popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
					});
					
				}else{
				
					if(jQuery("#custom_" + L.GeoCustomMarker.defaultIcon).length!=0){
				
						size = jQuery("#custom_" + L.GeoCustomMarker.defaultIcon).attr("size").split(",");
						
						customIcon = L.icon({
							iconUrl: jQuery("#custom_" + L.GeoCustomMarker.defaultIcon).attr("iconurl"),
							iconSize:     size,
						});
						
					}else{
					
						customIcon = L.icon({
							iconUrl: L.GeoCustomMarker.defaultMarker,
							shadowUrl: '',

							iconSize:     [25, 41], // size of the icon
							shadowSize:   [50, 64], // size of the shadow
							iconAnchor:   [12, 41], // point of the icon which will correspond to marker's location
							shadowAnchor: [4, 62],  // the same for the shadow
							popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
						});
					
					}
				
				}
				
				e.layer['options'].icon = customIcon;
				e.layer['options']['color'] = "";
				e.layer['options']['opacity'] = "";
				e.layer['options']['fillColor'] = "";
				e.layer['options']['fillOpacity'] = "";
				e.layer['options']['dashArray'] = "";
			
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
			var strokeColors = [];
			var fillColors = [];
			var strokeOpacity = [];
			var fillOpacity = [];
			var strokeType = [];
			var markerURLs = [];
			var markerSizes = [];
			counter = 0;
			drawnItems.eachLayer(function(layer) {
				strokeColors.push(layer['options']['color']);
				fillColors.push(layer['options']['fillColor']);
				fillOpacity.push(layer['options']['fillOpacity']);
				strokeOpacity.push(layer['options']['opacity']);
				strokeType.push(layer['options']['dashArray'].split(" ").join("--"));
				if(layer.options.icon!=undefined){
					markerURLs.push(layer.options.icon.options.iconUrl);	
					markerSizes.push(layer.options.icon.options.iconSize);	
				}		
			});
			
			console.log(markerSizes);
			
			jQuery("#EntryColors").val(fillColors.join(" "));
			jQuery("#EntryMarkers").val(markerURLs.join(" "));
			jQuery("#EntryMarkerSizes").val(markerSizes.join(" "));
			jQuery("#EntryStrokeColors").val(strokeColors.join(" "));
			jQuery("#EntryStrokeOpacity").val(strokeOpacity.join(" "));
			jQuery("#EntryFillOpacity").val(fillOpacity.join(" "));
			jQuery("#EntryStrokeType").val(strokeType.join(" "));
		}

		var getShapes = function(drawnItems, layerType) {
		
			var shapes = [];

			drawnItems.eachLayer(function(layer) {
			
				if ((layer instanceof L.Polyline) && !(layer instanceof L.Polygon)) {
					shapes.push('geoPOLYLINE='+layer.getLatLngs());
				}else if (layer instanceof L.Polygon) {
					shapes.push('geoPOLYGON='+layer.getLatLngs());
				}
				
				if (layer instanceof L.Circle) {
					shapes.push('geoCIRCLE='+[layer.getLatLng()]+'='+layer.getRadius());
				}
				
				if (layer instanceof L.Marker) {
					shapes.push('geoPOINT='+[layer.getLatLng()]);
				}
			});
			
			return shapes;
		};
		
		jQuery("#GeoLayerColor").spectrum({
			color: "rgba(255, 0, 0, .5)",
			allowEmpty: true,
			showAlpha: true,
			text: "Fill Color"
		});
		
		jQuery("#GeoStrokeColor").spectrum({
			color: "rgba(255, 0, 0, .5)",
			showAlpha: true,
			text: "Stroke Color"
		});
		
		map.drawColor = "#ff0000";
		map.drawOpacity = 0.5;
		map.strokeType = "1";
		
		jQuery("#GeoLayerColor").on('move.spectrum', function(e, tinycolor) { 
			if(tinycolor!=null){
				var t = jQuery("#GeoLayerColor").spectrum("get");
				map.drawColor = t.toHexString();
			}else{
				map.drawColor = "";
				map.drawOpacity = 0;
			}
		});
		
		jQuery("#GeoLayerColor").on('hide.spectrum', function(e, tinycolor) { 
			if(tinycolor!=null){
				var t = jQuery("#GeoLayerColor").spectrum("get");
				map.drawColor = t.toHexString();
			}else{
				map.drawColor = "";
				map.drawOpacity = 0;
			}
		});
		
		jQuery("#GeoLayerColor").on("dragstop.spectrum", function(e, color) {
			map.drawOpacity = color['_a'];
		});
		
		map.strokeColor = "#ff0000";
		map.strokeOpacity = 0.5;
		
		jQuery("#GeoStrokeColor").on('move.spectrum', function(e, tinycolor) { 
			var t = jQuery("#GeoStrokeColor").spectrum("get");
			map.strokeColor = t.toHexString(); 
		});
		
		jQuery("#GeoStrokeColor").on('hide.spectrum', function(e, tinycolor) { 
			var t = jQuery("#GeoStrokeColor").spectrum("get");
			map.strokeColor = t.toHexString(); 
		});
		
		jQuery("#GeoStrokeColor").on("dragstop.spectrum", function(e, color) {
			map.strokeOpacity = color['_a'];
		});
		
		jQuery('input[type=radio][name=GeoStrokeType]').change(function() {
			map.strokeType = this.value.split("--").join(" ");;
		});
	
	}
);