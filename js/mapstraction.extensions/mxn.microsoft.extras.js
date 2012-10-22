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
mxn.register( 'microsoft', {
	
	Marker: {
		toProprietary: function() {
			var mmarker = new VEShape(VEShapeType.Pushpin, this.location.toProprietary('microsoft'));
			//mmarker.SetTitle(this.labelText);
			mmarker.SetDescription(this.infoBubble);
			
			if (this.iconUrl) {
				var customIcon = new VECustomIconSpecification();
				customIcon.Image = this.iconUrl;
				// See this article on how to patch 6.2 to correctly render offsets.
				// http://social.msdn.microsoft.com/Forums/en-US/vemapcontroldev/thread/5ee2f15d-09bf-4158-955e-e3fa92f33cda?prof=required&ppud=4
				if (this.iconAnchor) {
				   customIcon.ImageOffset = new VEPixel(-this.iconAnchor[0], -this.iconAnchor[1]);
				} 
				else if (this.iconSize) {
				   customIcon.ImageOffset = new VEPixel(-this.iconSize[0]/2, -this.iconSize[1]/2);
				}
				if ((this.displayVisible) &&   (typeof(this.displayText) != 'undefined')) {
	    	        mmarker.SetCustomIcon("<img src='" + this.iconUrl + "'/>" + "<span class=" + this.displayClass + ">" + this.displayText + "</span>");
				}
				else {
					mmarker.SetCustomIcon(customIcon);
				} 				
			}
			if (this.draggable){
				mmarker.Draggable = true;
			}
			
			return mmarker;
		},
	
		openBubble: function() {
			if (!this.map) {
				throw 'Marker must be added to map in order to display infobox';
			}
			this.map.ShowInfoBox(this.proprietary_marker);
		},
		
		closeBubble: function() {
			if (!this.map) {
				throw 'Marker must be added to map in order to display infobox';
			}
			this.map.HideInfoBox();
		},
	
		hide: function() {
			this.proprietary_marker.Hide();
		},
	
		show: function() {
			this.proprietary_marker.Show();
		},
	
		update: function() {
			throw 'Not implemented';
		}
	},
	
    Mapstraction: {

		applyDataLayer: function(layerName,status) {
			var map = status ? this.maps[this.api] : null;
		
		},
    	
        addExtras: function() {
            var me = this;
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
