/*
 * L.Control.GeoCustomMarker - search for an address and zoom to its location
 * https://github.com/smeijer/leaflet.control.GeoCustomMarker
 */

L.GeoCustomMarker = {};
L.GeoCustomMarker.Provider = {};
L.GeoCustomMarker.defaultMarker = "";

L.GeoCustomMarker.Result = function (x, y, label) {
    this.X = x;
    this.Y = y;
    this.Label = label;
};

L.Control.GeoCustomMarker = L.Control.extend({
    options: {
        position: 'topright',
        showMarker: true
    },

    initialize: function (options) {
        L.Util.extend(this.options, options);
        L.Util.extend(this._config, options);
    },

    onAdd: function (map) {
	
        var $controlContainer = map._controlContainer,
            nodes = $controlContainer.childNodes,
            topCenter = false;

        if (!topCenter) {
            var tc = document.createElement('div');
            tc.className += 'leaflet-bottom leaflet-right';
            $controlContainer.appendChild(tc);
            map._controlCorners.topcenter = tc;
        }

        this._map = map;
        this._container = L.DomUtil.create('div', 'leaflet-control-GeoCustomMarker');
		
		list = "";
		first = "";
		for(x in customMarkers.customMarkers){
			if(first==""){
				first = customMarkers.customMarkers[x];
			}
			list += "<img id='icon_" + x + "' src='" + customMarkers.customMarkers[x] + "' />";
		}
		
        var searchbutton = document.createElement('div');
        searchbutton.id = 'leaflet-control-GeoCustomMarker-button';
        searchbutton.className = 'leaflet-control-GeoCustomMarker-button';
        searchbutton.innerHTML = '<p id="geocustommarkeropen"><img src="' + first + '" /></p><div id="geocustommarkers">' + list + '</div>';
        this._searchbutton = searchbutton;

		L.GeoCustomMarker.defaultMarker = first;

		L.DomEvent
		  .addListener(this._searchbutton.firstChild, 'click', this._onClickOpen, this);
		  
		L.DomEvent
		  .addListener(this._searchbutton.firstChild.nextSibling, 'click', this._onClickPic, this);  

        this._container.appendChild(this._searchbutton);

        L.DomEvent.disableClickPropagation(this._container);

        return this._container;
    },
	
    _onClickOpen: function (e) {
	
		if(e.target.parentNode.nextSibling.style.display=="block"){
			e.target.parentNode.nextSibling.style.display = "none";
		}else{
			e.target.parentNode.nextSibling.style.display = "block";
		}
        
    },
	
	_onClickPic: function (e) {

		document.getElementById("geocustommarkeropen").firstChild.src = e.target.src;
		L.GeoCustomMarker.defaultMarker = e.target.src;
		L.GeoCustomMarker.defaultIcon = jQuery(e.target).attr("id");
		
		console.log(L.GeoCustomMarker.defaultIcon);
		
		e.target.parentNode.style.display = "none";
    
    }
});
