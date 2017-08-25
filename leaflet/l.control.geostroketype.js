/*
 * L.Control.GeoStrokeType - search for an address and zoom to its location
 * https://github.com/smeijer/leaflet.control.GeoStrokeType
 */

L.GeoStrokeType = {};
L.GeoStrokeType.Provider = {};

L.GeoStrokeType.Result = function (x, y, label) {
    this.X = x;
    this.Y = y;
    this.Label = label;
};

L.Control.GeoStrokeType = L.Control.extend({
    options: {
        position: 'topright',
        showMarker: true
    },

    _config: {
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
        this._container = L.DomUtil.create('div', 'leaflet-control-GeoStrokeType');
		
        var searchbutton = document.createElement('div');
        searchbutton.id = 'leaflet-control-GeoStrokeType';
        searchbutton.className = 'leaflet-control-GeoStrokeType';
        innerHTML = '<label>___</label><input type="radio" title="solid border" value="1" name="GeoStrokeType" id="GeoStrokeType" checked /><br />';
        innerHTML += '<label>- -</label><input type="radio" title="dashed border" value="5--10" name="GeoStrokeType" id="GeoStrokeType" />';
        searchbutton.innerHTML = innerHTML;
        this._searchbutton = searchbutton;

        this._container.appendChild(this._searchbutton);

        L.DomEvent.disableClickPropagation(this._container);

        return this._container;
    },
	
});
