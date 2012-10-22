// Draggable markers extension to Mapstraction for Bing maps

/**
 * Add some Google v3 goodies to baseline mapstraction.
 */
mxn.addProxyMethods( mxn.Mapstraction, [
    /**
	 * Add a method that can be called to add our extra stuff to an implementation.
	 */
    'addExtras'
    ]);

// Amend baseline implementation
mxn.register( 'yahoo', {
    Mapstraction: {
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
