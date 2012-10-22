// Draggable markers extension to Mapstraction for Google V3 maps

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
mxn.register( 'googlev3', {

	Marker: {
		toProprietary: function() {
			var options = {};
	
			// do we have an Anchor?
			var ax = 0;  // anchor x 
			var ay = 0;  // anchor y
	
			if (this.iconAnchor) {
				ax = this.iconAnchor[0];
				ay = this.iconAnchor[1];
			}
			var gAnchorPoint = new google.maps.Point(ax,ay);
	
			if (this.iconUrl) {
	 			options.icon = new google.maps.MarkerImage(
					this.iconUrl,
					new google.maps.Size(this.iconSize[0], this.iconSize[1]),
					new google.maps.Point(0, 0),
					gAnchorPoint
				);
	
				// do we have a Shadow?
				if (this.iconShadowUrl) {
					if (this.iconShadowSize) {
						var x = this.iconShadowSize[0];
						var y = this.iconShadowSize[1];
						options.shadow = new google.maps.MarkerImage(
							this.iconShadowUrl,
							new google.maps.Size(x,y),
							new google.maps.Point(0,0),
							gAnchorPoint 
						);
					}
					else {
						options.shadow = new google.maps.MarkerImage(this.iconShadowUrl);
					}
				}
			}
			if (this.draggable) {
				options.draggable = this.draggable;
			}
			if (this.labelText) {
				options.title =  this.labelText;
			}
			if (this.imageMap) {
				options.shape = {
					coord: this.imageMap,
					type: 'poly'
				};
			}
			
			options.position = this.location.toProprietary(this.api);
			options.map = this.map;
	
			if (this.displayText){
				options.labelContent =  this.displayText;
				options.labelAnchor =  new google.maps.Point(0,-this.iconSize[1]);
				options.labelStyle = {opacity: 0.75};
			}
			if (this.displayClass){
				options.labelClass = this.displayClass; 
			}
			if ((this.displayVisible) &&   (typeof(this.displayText) != 'undefined')) {
				var marker = new MarkerWithLabel(options);
			}
			else {
				var marker = new google.maps.Marker(options);
			}
	
			if (this.infoBubble) {
				var event_action = "click";
				if (this.hover) {
					event_action = "mouseover";
				}
				google.maps.event.addListener(marker, event_action, function() {
					marker.mapstraction_marker.openBubble();
				});
			}
	
			if (this.hoverIconUrl) {
				var gSize = new google.maps.Size(this.iconSize[0], this.iconSize[1]);
				var zerozero = new google.maps.Point(0,0);
	 			var hIcon = new google.maps.MarkerImage(
					this.hoverIconUrl,
					gSize,
					zerozero,
					gAnchorPoint
				);
	 			var Icon = new google.maps.MarkerImage(
					this.iconUrl,
					gSize,
					zerozero,
					gAnchorPoint
				);
				google.maps.event.addListener(
					marker, 
					"mouseover", 
					function(){ 
						marker.setIcon(hIcon); 
					}
				);
				google.maps.event.addListener(
					marker, 
					"mouseout", 
					function(){ marker.setIcon(Icon); }
				);
			}
	
			google.maps.event.addListener(marker, 'click', function() {
				marker.mapstraction_marker.click.fire();
			});
			
			return marker;
		}
	},

    Mapstraction: {
    		
    	applyDataLayer: function(layerName,status) {
			var map = status ? this.maps[this.api] : null;
		
			switch(layerName) {
				case 'trafficLayer':
					this.trafficLayer.setMap(map);
					break;
			}		
    	},
    	
        addExtras: function() {
            var me = this;

			me.trafficLayer = new google.maps.TrafficLayer();

            me.polylineAdded.addHandler( function( name, source, args ) {
                args.polyline.rightclick = new mxn.Event( 'rightclick', args.polyline );
                google.maps.event.addListener( args.polyline.proprietary_polyline, 'rightclick', function() {
                    var points = args.polyline.proprietary_polyline.getPath();
                    console.log(args.polyline.proprietary_polyline);
                    console.log(points);
                    args.polyline.rightclick.fire( {
                        location: new mxn.LatLonPoint( points[0].lat(), points[0].lng() )
                    } );
                });
            });
                        
            me.markerAdded.addHandler( function( name, source, args ) {
                // enable dragend event for google
                args.marker.dragend = new mxn.Event( 'dragend', args.marker );
                google.maps.event.addListener( args.marker.proprietary_marker, 'dragend', function() {
                    var latlng = args.marker.proprietary_marker.getPosition();
                    args.marker.dragend.fire( {
                        location: new mxn.LatLonPoint( latlng.lat(), latlng.lng() )
                    } );
                });

                // enable dragstart event for google
                args.marker.dragstart = new mxn.Event( 'dragstart', args.marker );
                google.maps.event.addListener( args.marker.proprietary_marker, 'dragstart', function() {
                    var latlng = args.marker.proprietary_marker.getPosition();
                    args.marker.dragstart.fire( {
                        location: new mxn.LatLonPoint( latlng.lat(), latlng.lng() )
                    } );
                });

                // enable drag event for google
                args.marker.drag = new mxn.Event( 'drag', args.marker );
                google.maps.event.addListener( args.marker.proprietary_marker, 'drag', function() {
                    var latlng = args.marker.proprietary_marker.getPosition();
                    args.marker.drag.fire( {
                        location: new mxn.LatLonPoint( latlng.lat(), latlng.lng() )
                    } );
                });

                // enable right click event for google
                args.marker.rightclick = new mxn.Event( 'rightclick', args.marker );
                google.maps.event.addListener( args.marker.proprietary_marker, 'rightclick', function() {
                    var latlng = args.marker.proprietary_marker.getPosition();
                    args.marker.rightclick.fire( {
                        location: new mxn.LatLonPoint( latlng.lat(), latlng.lng() )
                    } );
                });

                // enable double click event for google
                args.marker.dblclick = new mxn.Event( 'dblclick', args.marker );
                google.maps.event.addListener( args.marker.proprietary_marker, 'dblclick', function() {
                    var latlng = args.marker.proprietary_marker.getPosition();
                    args.marker.dblclick.fire( {
                        location: new mxn.LatLonPoint( latlng.lat(), latlng.lng() )
                    } );
                });           

                // enable mouseout event for google
                args.marker.mouseover = new mxn.Event( 'mouseover', args.marker );
                google.maps.event.addListener( args.marker.proprietary_marker, 'mouseover', function() {
                    var latlng = args.marker.proprietary_marker.getPosition();
                    args.marker.mouseover.fire( {
                        location: new mxn.LatLonPoint( latlng.lat(), latlng.lng() )
                    } );
                });           

                // enable mouseout event for google
                args.marker.mouseout = new mxn.Event( 'mouseout', args.marker );
                google.maps.event.addListener( args.marker.proprietary_marker, 'mouseout', function() {
                    var latlng = args.marker.proprietary_marker.getPosition();
                    args.marker.mouseout.fire( {
                        location: new mxn.LatLonPoint( latlng.lat(), latlng.lng() )
                    } );
                });           
            });
        }
    }
});

