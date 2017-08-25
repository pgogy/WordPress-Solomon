/*
 * L.Control.GeoLayerColor - search for an address and zoom to its location
 * https://github.com/smeijer/leaflet.control.GeoLayerColor
 */

L.GeoLayerColor = {};
L.GeoLayerColor.Provider = {};

L.GeoLayerColor.Result = function (x, y, label) {
    this.X = x;
    this.Y = y;
    this.Label = label;
};

L.Control.GeoLayerColor = L.Control.extend({
    options: {
        position: 'topright',
        showMarker: true
    },

    _config: {
        country: '',
        searchLabel: 'search for address ...',
        notFoundMessage: 'Sorry, that address could not be found.',
        messageHideDelay: 3000,
        zoomLevel: 10
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
        this._container = L.DomUtil.create('div', 'leaflet-control-GeoLayerColor');
		
        var searchbutton = document.createElement('div');
        searchbutton.id = 'leaflet-control-GeoLayerColor-button';
        searchbutton.className = 'leaflet-control-GeoLayerColor-button';
        searchbutton.innerHTML = '<input type="text" id="GeoLayerColor" />';
        this._searchbutton = searchbutton;

        this._container.appendChild(this._searchbutton);

        L.DomEvent.disableClickPropagation(this._container);

        return this._container;
    },
	
    _onSearchClick: function (e) {
	
        var esc = 27,
            enter = 13,
            queryBox = document.getElementById('leaflet-control-GeoLayerColor-qry');

		var elem = this._resultslist;
		
		elem.innerHTML = "";

        this.GeoLayerColor(queryBox.value);
        
    }
});
