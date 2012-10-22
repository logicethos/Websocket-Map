// Draggable markers extension to Mapstraction for Openlayers maps

/**
 * Add logicethos goodies to baseline mapstraction.
 */
mxn.addProxyMethods( mxn.Mapstraction, [
    /**
	 * Add a method that can be called to add our extra stuff to an implementation.
	 */
    'addExtras',
    'applyDataLayer'
    ]);

// Amend baseline implementation
mxn.register( 'openlayers', {
	
	Marker: {
		toProprietary: function() {
			var size, anchor, icon;
			if(this.iconSize) {
				size = new OpenLayers.Size(this.iconSize[0], this.iconSize[1]);
			}
			else {
				size = new OpenLayers.Size(21,25);
			}

			if(this.iconAnchor) {
				anchor = new OpenLayers.Pixel(-this.iconAnchor[0], -this.iconAnchor[1]);
			}
			else {
				anchor = new OpenLayers.Pixel(-(size.w/2), -size.h);
			}

			if(this.iconUrl) {
				icon = new OpenLayers.Icon(this.iconUrl, size, anchor);
			}
			else {
				icon = new OpenLayers.Icon('http://openlayers.org/dev/img/marker-gold.png', size, anchor);
			}
			if ((this.displayVisible) && (typeof(OpenLayers.Marker.LabelMarker) != 'undefined')) {
				var marker = new OpenLayers.Marker.LabelMarker(this.location.toProprietary("openlayers"), icon, this.displayText);
			}
			else {
				var marker = new OpenLayers.Marker(this.location.toProprietary("openlayers"), icon);
			}

			if(this.infoBubble) {
				var popup = new OpenLayers.Popup(null,
					this.location.toProprietary("openlayers"),
					new OpenLayers.Size(100,100),
					this.infoBubble,
					true
				);
				popup.autoSize = true;
				var theMap = this.map;
				if(this.hover) {
					marker.events.register("mouseover", marker, function(event) {
						theMap.addPopup(popup);
						popup.show();
					});
					marker.events.register("mouseout", marker, function(event) {
						popup.hide();
						theMap.removePopup(popup);
					});
				}
				else {
					var shown = false;
					marker.events.register("mousedown", marker, function(event) {
						if (shown) {
							popup.hide();
							theMap.removePopup(popup);
							shown = false;
						} else {
							theMap.addPopup(popup);
							popup.show();
							shown = true;
						}
					});
				}
			}

			if(this.hoverIconUrl) {
				icon = this.iconUrl || 'http://openlayers.org/dev/img/marker-gold.png';
				hovericon = this.hoverIconUrl;
				marker.events.register("mouseover", marker, function(event) {
					marker.setUrl(hovericon);
				});
				marker.events.register("mouseout", marker, function(event) {
					marker.setUrl(icon);
				});
			}

			if(this.infoDiv){
				// TODO
			}
			return marker;
		},

		openBubble: function() {		
			// TODO: Add provider code
		},

		hide: function() {
			this.proprietary_marker.display( false );
		},

		show: function() {
			this.proprietary_marker.display( true );
		},

		update: function() {
			// TODO: Add provider code
		}		
	},
	
    Mapstraction: {

		applyDataLayer: function(layerName,status) {
			var map = status ? this.maps[this.api] : null;
		
			switch(layerName) {
			case 'seamark':
				this.layers.seamark.setVisibility(status);
				break;
			}		
    	},
    	
        addExtras: function() {
            var me = this;
            
            	me.layers.seamark = new OpenLayers.Layer.TMS(
				"Sea Markers",
				[ 
					"http://tiles.openseamap.org/seamark/",
				], 
				{ 
					type: 'png', 
					getURL: function (bounds) {
						var res = this.map.getResolution();
						var x = Math.round ((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
						var y = Math.round ((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
						var z = this.map.getZoom();
						var limit = Math.pow(2, z);
						if (y < 0 || y >= limit) {
							return null;
						} else {
							x = ((x % limit) + limit) % limit;
							var path = z + "/" + x + "/" + y + "." + this.type;
							var url = this.url;
							if (url instanceof Array) {
								url = this.selectUrl(path, url);
							}
							return url + path;
						}
					},
					displayOutsideMaxExtent: true, 
					isBaseLayer: false, 
					numZoomLevels: 18 	
				}
			); 
			
			var map = this.maps[this.api];
			map.addLayer(this.layers.seamark);			
			this.layers.seamark.setVisibility(false);
            
            me.markerAdded.addHandler( function( name, source, args ) {
                // enable dragend event for google
                args.marker.dragend = new mxn.Event( 'dragend', args.marker );

                // enable dragstart event for google
                args.marker.dragstart = new mxn.Event( 'dragstart', args.marker );

                // enable drag event for google
                args.marker.drag = new mxn.Event( 'drag', args.marker );
            });
        }
    }
});

