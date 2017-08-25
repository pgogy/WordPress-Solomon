L.Draw.ImageOverlay = L.Draw.Feature.extend({
        statics: {
            TYPE: "imageOverlay"
        },
        options: {
            icon: new L.Icon.Default,
            repeatMode: !1,
            zIndexOffset: 2e3
        },
        initialize: function(t, e) {
            this.type = L.Draw.Marker.TYPE, L.Draw.Feature.prototype.initialize.call(this, t, e)
        },
        addHooks: function() {
            L.Draw.Feature.prototype.addHooks.call(this), this._map && (this._tooltip.updateContent({
                text: L.drawLocal.draw.handlers.marker.tooltip.start
            }), this._mouseMarker || (this._mouseMarker = L.marker(this._map.getCenter(), {
                icon: L.divIcon({
                    className: "leaflet-mouse-marker",
                    iconAnchor: [20, 20],
                    iconSize: [40, 40]
                }),
                opacity: 0,
                zIndexOffset: this.options.zIndexOffset
            })), this._mouseMarker.on("click", this._onClick, this).addTo(this._map), this._map.on("mousemove", this._onMouseMove, this))
        },
        removeHooks: function() {
            L.Draw.Feature.prototype.removeHooks.call(this), this._map && (this._marker && (this._marker.off("click", this._onClick, this), this._map.off("click", this._onClick, this).removeLayer(this._marker), delete this._marker), this._mouseMarker.off("click", this._onClick, this), this._map.removeLayer(this._mouseMarker), delete this._mouseMarker, this._map.off("mousemove", this._onMouseMove, this))
        },
        _onMouseMove: function(t) {
            var e = t.latlng;
			
			if(jQuery("#custom_" + L.GeoCustomMarker.defaultIcon).length!=0){
				size = jQuery("#custom_" + L.GeoCustomMarker.defaultIcon).attr("size").split(",");
				
				customIcon = L.icon({
					iconUrl: jQuery("#custom_" + L.GeoCustomMarker.defaultIcon).attr("iconurl"),
					iconSize:     size,
				});
			}else{
				customIcon = this.options.icon;
			}
			
            this._tooltip.updatePosition(e), this._mouseMarker.setLatLng(e), this._marker ? (e = this._mouseMarker.getLatLng(), this._marker.setLatLng(e)) : (this._marker = new L.Marker(e, {
                icon: customIcon,
                zIndexOffset: this.options.zIndexOffset
            }), this._marker.on("click", this._onClick, this), this._map.on("click", this._onClick, this).addLayer(this._marker))
        },
        _onClick: function() {
            this._fireCreatedEvent(), this.disable(), this.options.repeatMode && this.enable()
        },
        _fireCreatedEvent: function() {
			current = "ImageOverlay";
            var t = new L.Marker(this._marker.getLatLng(), {
                icon: this.options.icon
            });
            L.Draw.Feature.prototype._fireCreatedEvent.call(this, t)
        }
    });