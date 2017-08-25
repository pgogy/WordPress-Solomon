/*
 * L.Control.GeoSearch - search for an address and zoom to its location
 * https://github.com/smeijer/leaflet.control.geosearch
 */

L.GeoSearch = {};
L.GeoSearch.Provider = {};

L.GeoSearch.Result = function (x, y, label) {
    this.X = x;
    this.Y = y;
    this.Label = label;
};

L.Control.GeoSearch = L.Control.extend({
    options: {
        position: 'topcenter',
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

        for (var i = 0, len = nodes.length; i < len; i++) {
            var klass = nodes[i].className;
            if (/leaflet-top/.test(klass) && /leaflet-center/.test(klass)) {
                topCenter = true;
                break;
            }
        }

        if (!topCenter) {
            var tc = document.createElement('div');
            tc.className += 'leaflet-top leaflet-center';
            $controlContainer.appendChild(tc);
            map._controlCorners.topcenter = tc;
        }

        this._map = map;
        this._container = L.DomUtil.create('div', 'leaflet-control-geosearch');

        var searchbox = document.createElement('input');
        searchbox.id = 'leaflet-control-geosearch-qry';
        searchbox.type = 'text';
        searchbox.placeholder = this._config.searchLabel;
        this._searchbox = searchbox;
		
        var searchbutton = document.createElement('div');
        searchbutton.id = 'leaflet-control-geosearch-button';
        searchbutton.className = 'leaflet-control-geosearch-button';
        searchbutton.innerHTML = 'Search';
        this._searchbutton = searchbutton;

		var closebutton = document.createElement('div');
        closebutton.id = 'leaflet-control-geosearch-close';
        closebutton.className = 'leaflet-control-geosearch-close';
        closebutton.innerHTML = 'Close';
        closebutton.setAttribute("style","display:none");
        this._closebutton = closebutton;


        var msgbox = document.createElement('div');
        msgbox.id = 'leaflet-control-geosearch-msg';
        msgbox.className = 'leaflet-control-geosearch-msg';
        this._msgbox = msgbox;

        var resultslist = document.createElement('ul');
        resultslist.id = 'leaflet-control-geosearch-results';
        this._resultslist = resultslist;

        this._msgbox.appendChild(this._resultslist);
        this._container.appendChild(this._searchbox);
        this._container.appendChild(this._searchbutton);
        this._container.appendChild(this._closebutton);
        this._container.appendChild(this._msgbox);

        L.DomEvent
          .addListener(this._container, 'click', L.DomEvent.stop)
		  .addListener(this._searchbutton, 'click', this._onSearchClick, this)
		  .addListener(this._closebutton, 'click', this._onSearchClose, this)
		  .addListener(this._searchbox, 'KeyUp', this._onKeyUp, this);

        L.DomEvent.disableClickPropagation(this._container);

        return this._container;
    },

    geosearch: function (qry) {
        try {
            var provider = this._config.provider;

            if(typeof provider.GetLocations == 'function') {
                var results = provider.GetLocations(qry, function(results) {
                    this._processResults(results);
                }.bind(this));
            }
            else {
                var url = provider.GetServiceUrl(qry);
                this.sendRequest(provider, url);
            }
        }
        catch (error) {
            this._printError(error);
        }
    },

    sendRequest: function (provider, url) {
        var that = this;

        window.parseLocation = function (response) {
            var results = provider.ParseJSON(response);
            that._processResults(results);

            document.body.removeChild(document.getElementById('getJsonP'));
            delete window.parseLocation;
        };

        function getJsonP (url) {
            url = url + '&callback=parseLocation'
            var script = document.createElement('script');
            script.id = 'getJsonP';
            script.src = url;
            script.async = true;
            document.body.appendChild(script);
        }

        if (XMLHttpRequest) {
            var xhr = new XMLHttpRequest();

            if ('withCredentials' in xhr) {
                var xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4) {
                        if (xhr.status == 200) {
                            var response = JSON.parse(xhr.responseText),
                                results = provider.ParseJSON(response);

                            that._processResults(results);
                        } else if (xhr.status == 0 || xhr.status == 400) {
                            getJsonP(url);
                        } else {
                            that._printError(xhr.responseText);
                        }
                    }
                };

                xhr.open('GET', url, true);
                xhr.send();
            } else if (XDomainRequest) {
                var xdr = new XDomainRequest();

                xdr.onerror = function (err) {
                    that._printError(err);
                };

                xdr.onload = function () {
                    var response = JSON.parse(xdr.responseText),
                        results = provider.ParseJSON(response);

                    that._processResults(results);
                };

                xdr.open('GET', url);
                xdr.send();
            } else {
                getJsonP(url);
            }
        }
    },

    _processResults: function(results) {
	
        if (results.length > 0) {
            //this._map.fireEvent('geosearch_foundlocations', {Locations: results});
            if (results.length > 1) {
				this._printResults(results);
			}else{
				this._showLocation(results[0]);
			}
        } else {
            this._printError(this._config.notFoundMessage);
        }
    },

    _showLocation: function (location) {
        if (this.options.showMarker == true) {
            if (typeof this._positionMarker === 'undefined')
                this._positionMarker = L.marker([location.Y, location.X]).addTo(this._map);
            else
                this._positionMarker.setLatLng([location.Y, location.X]);
        }

        this._map.setView([location.Y, location.X], this._config.zoomLevel, false);
        this._map.fireEvent('geosearch_showlocation', {Location: location});
    },

	_printResults: function(results) {
	
        var elem = this._resultslist;
		
		for(result in results){
				
			var node = document.createElement("LI");                 
			var textnode = document.createTextNode(results[result]['Label']);  
			node.appendChild(textnode);                              
			node.setAttribute("X", results[result]['X']);
			node.setAttribute("Y", results[result]['Y']);
		
			elem.appendChild(node); 	
			
			L.DomEvent
				.addListener(node, 'click', this._onLocationClick, this);
		
		}
		
        elem.style.display = 'block';
		
		this._closebutton.setAttribute("style","display:inline-block");

    },
	
	_onLocationClick: function(data) {
		
		this._map.setView([data.target.getAttribute("y"), data.target.getAttribute("x")], this._config.zoomLevel, false);
		
	},

    _printError: function(message) {
        var elem = this._resultslist;
        elem.innerHTML = '<li>' + message + '</li>';
        elem.style.display = 'block';

        setTimeout(function () {
            elem.style.display = 'none';
        }, 5000);
    },
	
    _onKeyUp: function (e) {
	
        var esc = 27,
            enter = 13,
            queryBox = document.getElementById('leaflet-control-geosearch-qry');

        if (e.keyCode === esc) { // escape key detection is unreliable
            queryBox.value = '';
            this._map._container.focus();
        } 
    },

	_onSearchClose: function (e) {
	
        var elem = this._resultslist;
        var elemtwo = this._closebutton;
		
        setTimeout(function () {
            elem.style.display = 'none';
            elemtwo.style.display = 'none';
        }, 50);

        
    },
	
    _onSearchClick: function (e) {
	
        var esc = 27,
            enter = 13,
            queryBox = document.getElementById('leaflet-control-geosearch-qry');

		var elem = this._resultslist;
		
		elem.innerHTML = "";

        this.geosearch(queryBox.value);
        
    }
});
