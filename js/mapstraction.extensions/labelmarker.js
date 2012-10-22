/** 
 * @requires OpenLayers/Marker.js 
 * 
 * Class: OpenLayers.Marker.LabelMarker 
 * 
 * Inherits from: 
 *  - <OpenLayers.Marker>  
 */ 
OpenLayers.Marker.LabelMarker = OpenLayers.Class(OpenLayers.Marker, { 

    // 
    // Property: label 
    // {String} Marker label. 
    // 
    label: "", 
    
    markerDiv: null, 
    
    initialize: function(lonlat, icon, label) { 
        OpenLayers.Marker.prototype.initialize.apply(this, [lonlat, icon]); 
        this.markerDiv = OpenLayers.Util.createDiv();
  		this.markerDiv.innerHTML = label;
  		this.markerDiv.className = "displayText";
        this.icon.imageDiv.appendChild(this.markerDiv);
    }, 

    // 
    // Method: destroy 
    // Nullify references and remove event listeners to prevent circular 
    // references and memory leaks 
    // 
    destroy: function() { 
        this.markerDiv.innerHTML = ""; 
        this.markerDiv = null;
        OpenLayers.Marker.prototype.destroy.apply(this, arguments);
    }, 
    
    CLASS_NAME: "OpenLayers.Marker.LabelMarker" 
}); 