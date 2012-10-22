// Draggable markers extension to Mapstraction for Yahoo maps

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
mxn.register( 'microsoft7', {
	
	Marker: {	
		toProprietary: function() {
			var options = {};
			if (this.draggable)
			{
				options.draggable = true;
			}
			var ax = 0;	// anchor x 
			var ay = 20;	// anchor y
	
			if (this.iconAnchor) {
				ax = this.iconAnchor[0];
				ay = this.iconAnchor[1];
			}
			var mAnchorPoint = new Microsoft.Maps.Point(ax,ay);
			if (this.iconUrl) {
				options.icon = this.iconUrl;
				options.height = this.iconSize[1]+20;
				options.width = this.iconSize[0];
				options.anchor = mAnchorPoint;
			}

			if (this.label)
			{
				options.text = this.label;
			}

			if ((this.displayVisible) && (typeof(this.displayText) != 'undefined')) {
 				options.text = this.displayText;
				options.textOffset = new Microsoft.Maps.Point(0,25);
				options.typeName = this.displayClass + "Bing7";
			}

			var mmarker = new Microsoft.Maps.Pushpin(this.location.toProprietary('microsoft7'), options); 

			if (this.infoBubble){
				var event_action = "click";
				if (this.hover) {
					event_action = "mouseover";
				}
				Microsoft.Maps.Events.addHandler(mmarker, event_action, function() {
					mmarker.mapstraction_marker.openBubble();
				});
				/*
				Microsoft.Maps.Events.addHandler(this.map, 'viewchange', function () {
					mmarker.mapstraction_marker.closeBubble();
				});
				*/
			}
			return mmarker;
		},

		openBubble: function() {	
			infobox.setLocation(this.proprietary_marker._location);
			infobox.setOptions({
								visible: true, 
								height: 220,
								description: this.infoBubble 
								});								
			this.proprietary_infowindow = infobox; // Save so we can close it later
		},
		
		closeBubble: function() {
			if (!this.map) {
				throw 'Marker must be added to map in order to display infobox';
			}
			if (!this.proprietary_infowindow) {
				return;
			}
			this.proprietary_infowindow.setOptions({visible:false});
			this.map.entities.remove(this.proprietary_infowindow);
		}
	},
	

	Polyline: {	
		toProprietary: function() {
			var points = [];
			for (var i = 0, length = this.points.length; i < length; i++) {
				points.push(this.points[i].toProprietary(this.api));
			}
			
			var strokeColor = Microsoft.Maps.Color.fromHex(this.color || '#000000');
			strokeColor.a = (this.opacity || 1.0) * 255;
			var fillColor = Microsoft.Maps.Color.fromHex(this.fillColor || '#000000');
			fillColor.a = (this.fillOpacity || 1.0) * 255;
			
			var polyOptions = {
				strokeColor: strokeColor,
				strokeThickness: (this.width || 3)
			};
	
			if (this.closed) {
				polyOptions.fillColor = fillColor;
				points.push(this.points[0].toProprietary(this.api));
				return new Microsoft.Maps.Polygon(points, polyOptions);
			}
			else {
				return new Microsoft.Maps.Polyline(points, polyOptions);
			}
	
		},
		
		show: function() {
			this.proprietary_polyline.setOptions({visible:true});
		},
	
		hide: function() {
			this.proprietary_polyline.setOptions({visible:false});
		}	
	},

    Mapstraction: {

		applyDataLayer: function(layerName,status) {
			var map = status ? this.maps[this.api] : null;	
		},
		
		getCenter: function() {
			var point;
			var map = this.maps[this.api];
			var location = map.getCenter();
			point =  new mxn.LatLonPoint(location.latitude,location.longitude);
		
			return point;
		},

    	    	 
        addExtras: function() {
            var me = this;
            
 			var map = this.maps[this.api];	
			var infoboxLayer = new Microsoft.Maps.EntityCollection();
			map.entities.push(infoboxLayer);
			
			infobox = new Microsoft.Maps.Infobox(new Microsoft.Maps.Location(0, 0), { visible: false, offset: new Microsoft.Maps.Point(0, 20) });
			infoboxLayer.push(infobox);
        }
    }
});
