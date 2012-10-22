<!---
Copyright (c) 2012, Logic Ethos Ltd, logicethos.com.
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
   derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
--->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<title>WebSockets Map</title>
       
<link type="text/css" rel="stylesheet" href="css/layout-default-latest.css" />
<link type='text/css' rel='stylesheet' href='css/smoothness/jquery-ui-1.8.16.custom.css' id="jqueryuitheme" media='screen' />
<link type='text/css' rel='stylesheet' href='css/jquery.ui.selectmenu.css' media='screen' />

<link type="text/css" rel="stylesheet" href="css/ui.jqgrid.css" media="screen"/> 
<link type="text/css" rel="stylesheet" href="css/jquery.colorpicker.css" media="screen"/> 

<link rel="stylesheet" href="css/site.css" type="text/css">  
        
<script src="js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
<script src="js/raphael-min.js" type="text/javascript"></script>
<script src="js/jquery.layout-latest.js" type="text/javascript"></script>
<script src="js/jquery.timers.js" type="text/javascript"></script>

<script src="js/underscore-min.js" type="text/javascript"></script>

<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6"></script>
<script src="http://openlayers.org/api/OpenLayers.js"></script>
<script src="http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=7.0" type="text/javascript"></script>

<script type="text/javascript" charset="utf-8" src="js/mapstraction.2.0.18/mxn.js?(googlev3, microsoft, microsoft7, openlayers)"></script>
<script type="text/javascript" charset="utf-8" src="js/mapstraction.2.0.18/mxn.geocoder.js?(googlev3)"></script>
<script type="text/javascript" charset="utf-8" src="js/mapstraction.extensions/mxn.googlev3.geocoder.js"></script>

<script src="js/mapstraction.extensions/mxn.googlev3.extras.js" type="text/javascript"></script>
<script src="js/mapstraction.extensions/mxn.microsoft.extras.js" type="text/javascript"></script>
<script src="js/mapstraction.extensions/mxn.openlayers.extras.js" type="text/javascript"></script>
<script src="js/mapstraction.extensions/mxn.microsoft7.extras.js" type="text/javascript"></script>
<script src="js/mapstraction.extensions/mxn.marker.extras.js" type="text/javascript"></script>
<script src="js/mapstraction.extensions/markerwithlabel.js" type="text/javascript"></script>
<script src="js/mapstraction.extensions/labelmarker.js" type="text/javascript"></script>

<script src="js/grid.locale-en.js" type="text/javascript"></script>
<script src="js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script src="js/plugins/jquery.contextmenu.js" type="text/javascript"></script>

<script src="js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
<script src="js/jquery.ui.selectmenu.js" type="text/javascript"></script>

<script src="js/jquery.colorpicker.js" type="text/javascript"></script>
<script src="js/jquery.scrollbarwidth.js" type="text/javascript"></script>

<script src="js/jquery-cookie.js" type="text/javascript"></script>

<script src="js/tracking.js" type="text/javascript"></script>

<script src="js/fg.menu.js" type="text/javascript"></script>    
<link type="text/css" href="css/fg.menu.css" media="screen" rel="stylesheet" />

<!---script type="text/javascript" src="http://map.openseamap.org/map/javascript/harbours.js"></script--->
<script type="text/javascript" src="http://map.openseamap.org/map/javascript/map_utils.js"></script>

<style type="text/css" media="screen">
	.mapstraction {
		height: 600px;
		width: 100%;
		z-index: 1;
  }
</style>

<script type="text/javascript">
	//---------------------------------------------------------------------------------------------//
	// Globals
	//---------------------------------------------------------------------------------------------//
	var mxnObj;						// map abstraction object
	var geocoderObj;				// map geocoder object
	var geocoderReverseObj;			// map geocoder reverse
    var directionsServiceObj;		// directions service object
    var directionsDisplayObj;		// directions display object
	var layoutObj;					// jquery layout object
	var journeyObj;					// object retaining last retrieved journey list
	var searchMarkerObj;			// search marker objects
	var websocketsActive = false;	// logon flag
	var username = "";				// hold username
	var password = "";				// and password so if we are kicked of it is retained
	var mapHomeCenter 		= new mxn.LatLonPoint(constants.DEFAULT_LATITUDE, constants.DEFAULT_LONGITUDE);	// map home default
	var mapHomeZoom 		= constants.DEFAULT_MAPZOOM;	// map zoom default
	var mapFindZoom 		= constants.DEFAULT_FINDZOOM;	// map zoom level after find
	var lastTrackerTime 	= 0;		// last tracker time returned
	var settingsReturned 	= false;	// setting keyvalues have been returned, they won't be if this is first logon
	var displayState 		= constants.DISPLAY_TRACKING
	var selectedJourneyID 	= null;	// selected journey, this global is unfortunatly necessary to avoid issues with the selectmenu
	var currentPDF;					// the current available PDF
	var BingKey 			= "ApvVuh6ECQSifRbepGZaHrrENcQzSpU2MuyPYq5Hic2jtOigIZ-enFIDiP-FsjPZ"; 
	var microsoft_key = BingKey;
	var routePoly;					// route polygon
	var debugState = true;			// debug status

	// set EVERY 'state' here so will undo ALL layout changes used by the 'Reset State' button: layoutObj.loadState( stateResetSettings )
	var stateResetSettings = {
			north__size:		45	//"auto"
		,	north__initClosed:	false
		,	north__initHidden:	false
		,	south__size:		0
		,	south__initClosed:	true
		,	south__initHidden:	true
		,	west__size:			0
		,	west__initClosed:	true
		,	west__initHidden:	true
		,	east__size:		450
		,	east__initClosed:	false
		,	east__initHidden:	false
	};
	
	//---------------------------------------------------------------------------------------------//
	// Utility functions
	//---------------------------------------------------------------------------------------------//
	function formatTrackerDate(datevalue) {
		var d = new Date(946684800000 +  (datevalue * 100));
		return $.datepicker.formatDate('yy/mm/dd ', d)+d.toLocaleTimeString(); //TODO check that date is Local
	}
	
	function formatTrackerTime(datevalue) {
		var d = new Date(946684800000 +  (datevalue * 100));
		return d.toLocaleTimeString().substring(0,5); //TODO check that date is Local
	}
	
	function formatBooleanOnOff(booleanValue) {
		return (booleanValue) ? 'On' : 'Off';
	}

	function formatSpeed(kmphValue,includeunits) {
		var speed = '';
		if (kmphValue>0) {
			switch ($("#optSpeedUnits").val()) {
				case 'mile':
					speed = Math.floor(kmphValue * 0.621).toString();
					if (includeunits) speed += ' mph';
					break;
				case 'km':
					speed = kmphValue.toString();
					if (includeunits) speed += ' kph';
					break;
				case 'nautical':
					speed = Math.floor(kmphValue * 0.540).toString();
					if (includeunits) speed += ' knots';
					break;
			}
		}
		return speed;
	}

	function formatDistance(kmValue) {
		var distance = '';
		switch ($("#optSpeedUnits").val()) {
			case 'mile':
				kmValue *= 0.621;
				distance = kmValue.toFixed(1) + ' miles';
				break;
			case 'km':
				distance = kmValue.toFixed(1) + ' km';
				break;
			case 'nautical':
				kmValue *= 0.540;
				distance = kmValue.toFixed(1) + ' N Mile';
				break;
		}
		return distance;
	}

	function formatLongitude(longitudeValue) {
		if (typeof(longitudeValue)=='string') {
			var longitudeValue = parseFloat(longitudeValue);
		}
		return (longitudeValue > 0) ? ('E ' + longitudeValue.toFixed(4).toString()) : ('W ' + (-1*longitudeValue.toFixed(4)).toString());
	}

	function formatLatitude(latitudeValue) {
		if (typeof(latitudeValue)=='string') {
			var latitudeValue = parseFloat(latitudeValue);
		}
		return (latitudeValue > 0) ? ('N ' + latitudeValue.toFixed(4).toString()) : ('S ' + (-1*latitudeValue.toFixed(4)).toString());
	}
	
	function formatAltitude(altitudeValue) {
		return altitudeValue;
	}
	
	function formatDirection(directionValue) {
		return directionValue + '&deg;';
	}	

	//---------------------------------------------------------------------------------------------//
	// Mapstraction functions
	//---------------------------------------------------------------------------------------------//
	function create_map() {
		openMapAPI();
		
		mxnObj.setCenterAndZoom(mapHomeCenter, mapHomeZoom);
		
		mxnObj.addControls({
        	pan: true, 
	        zoom: 'large',
			overview: true,
			scale: true,
	        map_type: true
	    });
	    
	    // add geocoder
   		geocoderObj = new mxn.Geocoder('googlev3', geocode_return, error_callback);
   		// add reverse geocoder
   		geocoderReverseObj = new mxn.Geocoder('googlev3', geocodereverse_return, error_callback_reverse);    		

		// directions service 
		directionsServiceObj = new google.maps.DirectionsService();
		
		// directions display
     	directionsDisplayObj = new google.maps.DirectionsRenderer();
		directionsDisplayObj.setMap(mxnObj.maps.googlev3);
		directionsDisplayObj.setPanel(document.getElementById('directionsResultsDisplay'));
 	}
 	
 	function calculateGridWidth() {
 		return layoutObj.state.east.size-($.scrollbarWidth()+6);
 	}
	
	function resize_map() {
	    var width  = layoutObj.state.center.innerWidth;  
	    var height = layoutObj.state.center.innerHeight; 
		mxnObj.resizeTo(width,height);
				
		$("#vehiclelist").jqGrid('setGridWidth',calculateGridWidth());
		$("#journeylist").jqGrid('setGridWidth',calculateGridWidth());
		$("#staticlist").jqGrid('setGridWidth',calculateGridWidth());
	}
	
	function openMapAPI() {
		switch ('[var.mapapi]') {
			case 'google':
					mxnObj = new mxn.Mapstraction('googlev3', 'googlev3');
					$("#googlev3").show();
					// this breaks microsoft, so set individually
					mxnObj.maps.googlev3.scrollwheel = true;				
				    resize_map();
					break;
			case 'yahoo':
					mxnObj = new mxn.Mapstraction('yahoo', 'yahoo');
					$("#yahoo").show();
				    //resize_map(); - fails with error for yahoo
					break;
			case 'microsoft':
					mxnObj = new mxn.Mapstraction('microsoft', 'microsoft');
					$("#microsoft").show();
				    resize_map();
					break;
			case 'microsoft7':
					mxnObj = new mxn.Mapstraction('microsoft7', 'microsoft7');
					$("#microsoft7").show();
				    resize_map();
					break;
			default:
					mxnObj = new mxn.Mapstraction('openlayers', 'openlayers');
					$("#openlayers").show();
					var navigators = mxnObj.maps.openlayers.getControlsByClass( 'OpenLayers.Control.Navigation' );
					navigators[0].enableZoomWheel();							
				    resize_map();
					break;
		}
		mxnObj.addExtras();

		mxnObj.click.addHandler(function(event_name, event_source, event_args) {
			var p = event_args.location;
			$('#clickPosition').html(formatLatitude(p.lat) + ', ' + formatLongitude(p.lon));
	    });
	}
	  
	function changeMapAPI() {
		// remove polylines
		mxnObj.removeAllPolylines();

		try {	
			switch ($('#optApi').val()) {
				case 'google':
						mxnObj.swap('googlev3','googlev3');
						mxnObj.maps.googlev3.scrollwheel = true;				
						break;
				case 'yahoo':
						mxnObj.swap('yahoo','yahoo');
						break;
				case 'microsoft':
						mxnObj.swap('microsoft','microsoft');
						break;
				case 'microsoft7':
						mxnObj.swap('microsoft7','microsoft7');
						break;
				default:
						mxnObj.swap('openlayers','openlayers');
						var navigators = mxnObj.maps.openlayers.getControlsByClass( 'OpenLayers.Control.Navigation' );
						navigators[0].enableZoomWheel();							
						break;
			}
		}
		catch(err) {
			console.log(err);
		}		

		// resize
	    resize_map();
	    	    
		// traffic 
		mxnObj.addExtras();
		mxnObj.applyDataLayer('trafficLayer',($("#optShowTraffic").val()=='On'));
		mxnObj.applyDataLayer('seamark',($("#optShowSeamap").val()=='On'));
	}
	
	function getMarkerIcon(iconid,direction) {
		var markerName = 'img/icons/';
		if (iconid < constants.MARKERS.length) {
			markerName += constants.MARKERS[iconid];
		} 
		else {
			markerName += $("#optMarkerType").val();
		}
		markerName += (direction < 180) ? '_E.png' : '_W.png';
		
		return markerName;
	}
	
	// hides a disconnected marker
	function disconnectMarker(data) {
		var id = data.disconnected.id;
		if (!($('#optShowInactive').val()=="On" )) {
			mxnObj.removeMarker(TrackingService.accounts[id].mapObject);
			TrackingService.accounts[id].mapObject = null;
		}
	}
	
	// shows a connected marker
	function connectMarker(item) {
		var data = new Object();
		data.id = item.connected.id;
		data.gps = new Object();
		data.ignOn = $("#vehiclelist").getCell(data.id,'ignOn');
		data.loggedIn = $("#vehiclelist").getCell(data.id,'connected');
		data.gps.latitude = $("#vehiclelist").getCell(data.id,'latitude');
		data.gps.longitude = $("#vehiclelist").getCell(data.id,'longitude');
		data.gps.altitude = $("#vehiclelist").getCell(data.id,'altitude');
		data.gps.direction = $("#vehiclelist").getCell(data.id,'direction');
		data.gps.speedKm = $("#vehiclelist").getCell(data.id,'kph');
		data.odoM = $("#vehiclelist").getCell(data.id,'odom');
		showMarker(data);

	}

	function showMarker(data) {
		if( typeof(TrackingService.accounts[data.id]) != 'undefined' ) {
			if (TrackingService.accounts[data.id].mapObject != null) {  
				mxnObj.removeMarker(TrackingService.accounts[data.id].mapObject);
			    TrackingService.accounts[data.id].mapObject = null; 
			}
			if (displayState == constants.DISPLAY_TRACKING) {
				if (($('#optShowInactive').val()=="On" ) || (TrackingService.accounts[data.id].connected)) {
					try {
						var markerObj = new mxn.Marker( new mxn.LatLonPoint(data.gps.latitude,data.gps.longitude));
					
						var infoBubble = 	"<div class='infobubble'>" +
											"<table>" +
											"<tr><td>Name: </td><td>" + TrackingService.accounts[data.id].name + "</td></tr>" +
											"<tr><td>ID: </td><td>" + data.id + "</td></tr>" +
											"<tr><td>Ignition: </td><td>" + formatBooleanOnOff(data.ignOn) + "</td></tr>" +
											"<tr><td>Logged In: </td><td>" + formatBooleanOnOff(data.loggedIn) + "</td></tr>" +
											"<tr><td>Odo Km: </td><td>" + data.odoM + "</td></tr>" +
											"<tr><td>Latitude: </td><td>" + formatLatitude(data.gps.latitude) + "</td></tr>" +
											"<tr><td>Longitude: </td><td>" + formatLongitude(data.gps.longitude) + "</td></tr>" +
											"<tr><td>Altitude: </td><td>" + formatAltitude(data.gps.altitude) + "</td></tr>" +
											"<tr><td>Speed: </td><td>" + formatSpeed(data.gps.speedKm,true) + "</td></tr>" +
											"<tr><td>Direction: </td><td>" + formatDirection(data.gps.direction) + "</td></tr>" +
											"</table>" +
											"</div>";
					
						markerObj.setInfoBubble(infoBubble);

						var markerName = getMarkerIcon($("#vehiclelist").getCell(data.id,'icon'),data.gps.direction);
						markerObj.setIcon(markerName,[45,31]);
						markerObj.setLabel($("#vehiclelist").getCell(data.id,'name'));

						// Start extension to mapstraction
						markerObj.setDisplayText($("#vehiclelist").getCell(data.id,'name'));
						markerObj.setDisplayClass('displayText');
						var visibleMarkers = ($("#optShowLabels").val()=="On");
						markerObj.setDisplayVisible(visibleMarkers);
						// End extension for mapstraction
						mxnObj.addMarker(markerObj);
						
						TrackingService.accounts[data.id].mapObject = markerObj;
					}
					catch(err) {
						console.log('ERROR ' + data.id);
					}
				}
			}
		}		
	}
	
	function clearMarkers() {
		var vehiclelist = $("#vehiclelist").getDataIDs();
		for(i=0;i<vehiclelist.length;i++){
			var id = vehiclelist[i];
			if (TrackingService.accounts[id].mapObject != null) {  
				mxnObj.removeMarker(TrackingService.accounts[id].mapObject);
			    TrackingService.accounts[id].mapObject = null;
			}
		}
		if (searchMarkerObj != null) mxnObj.removeMarker(searchMarkerObj);
	}
	
	function reloadMarkers() {
		var vehiclelist = $("#vehiclelist").getDataIDs();
		var data = new Object();
		data.gps = new Object();
		for(i=0;i<vehiclelist.length;i++){
			data.id = vehiclelist[i];
			data.ignOn = $("#vehiclelist").getCell(data.id,'ignOn');
			data.loggedIn = $("#vehiclelist").getCell(data.id,'connected');
			data.gps.latitude = $("#vehiclelist").getCell(data.id,'latitude');
			data.gps.longitude = $("#vehiclelist").getCell(data.id,'longitude');
			data.gps.altitude = $("#vehiclelist").getCell(data.id,'altitude');
			data.gps.direction = $("#vehiclelist").getCell(data.id,'direction');
			data.gps.speedKm = $("#vehiclelist").getCell(data.id,'kph');
			data.odoM = $("#vehiclelist").getCell(data.id,'odom');
			showMarker(data);
		}
	}
	
	function clearJourneys() {
		if (typeof(journeyObj) != 'undefined') {
			for (j=0;j<journeyObj._journeyList.length;j++) {
				if (typeof(journeyObj._journeyList[j].polyline) != 'undefined') {
					mxnObj.removePolyline(journeyObj._journeyList[j].polyline);
					mxnObj.removeMarker(journeyObj._journeyList[j].endmarker);
					mxnObj.removeMarker(journeyObj._journeyList[j].startmarker);
					journeyObj._journeyList[j].polyline = null;
					journeyObj._journeyList[j].endmarker = null;
					journeyObj._journeyList[j].startmarker = null;
				}					
			}
		}
	}

	//---------------------------------------------------------------------------------------------//
	// Search functions
	//---------------------------------------------------------------------------------------------//
	function error_callback(status) {
		switch (status) {
			case 'ZERO_RESULTS':
					$( "#dialog-searchfailed" ).dialog( "open" );
					break;
			default:
				console.log(status);
		}
	}
	
	function searchSetCenter(latitude,longitude,text) {
		mxnObj.setCenterAndZoom(new mxn.LatLonPoint(latitude,longitude), (mapFindZoom<=16 || $('#optApi').val()!='mapquest') ?  mapFindZoom: 16);
		if (searchMarkerObj != null) mxnObj.removeMarker(searchMarkerObj); 
		searchMarkerObj = new mxn.Marker(new mxn.LatLonPoint(latitude,longitude));    
                searchMarkerObj.setIcon('img/magnify_glass.png',[23,23],[4,30]);
                searchMarkerObj.setDisplayText(text);
                searchMarkerObj.setDisplayVisible(true);
                mxnObj.addMarker(searchMarkerObj);
	}
	
	function searchSetJourneyFrom(targetIDNo) {
		var spanid = "#searchresult" + targetIDNo;
		$("#txtCalculateFrom").val($(spanid).text());
		$( "#dialog-searchmultiple" ).dialog( "close" );
	}

	function searchSetJourneyTo(targetIDNo) {
		var spanid = "#searchresult" + targetIDNo;
		$("#txtCalculateTo").val($(spanid).text());
		$( "#dialog-searchmultiple" ).dialog( "close" );
	}

	function searchSetJourneyVia(targetIDNo) {
		var spanid = "#searchresult" + targetIDNo;
		$("#txtCalculateVia").val($(spanid).text());
		$( "#dialog-searchmultiple" ).dialog( "close" );
	}
	
	function doSearchSetJourneyFromFind() {
		$('#intCalculateFromLat').val("");
		$('#intCalculateFromLon').val("");
		searchLocation( $('#txtCalculateFrom').val(),'#txtCalculateFrom');
	}

	function doSearchSetJourneyToFind() {
		$('#intCalculateToLat').val("");
		$('#intCalculateToLon').val("");
		searchLocation( $('#txtCalculateTo').val(),'#txtCalculateTo');
	}

	function doSearchSetJourneyViaFind() {
		$('#intCalculateViaLat').val("");
		$('#intCalculateViaLon').val("");
		searchLocation( $('#txtCalculateVia').val(),'#txtCalculateVia');
	}
	
	function doSearchSetJourneyFromItem() {
		$('#destinationpick').val('from');
		$( "#dialog-location" ).dialog( "open" );
	}

	function doSearchSetJourneyToItem() {
		$('#destinationpick').val('to');
		$( "#dialog-location" ).dialog( "open" );
	}

	function doSearchSetJourneyViaItem() {
		$('#destinationpick').val('via');
		$( "#dialog-location" ).dialog( "open" );
	}

	function geocode_return(location) {
		if (location.length==1) {
			switch ($("#txtSearchSource").val()) {
				case '#txtCalculateFrom':
					$("#txtCalculateFrom").val(location[0].formatted_address);
					break;
				case '#txtCalculateTo':
					$("#txtCalculateTo").val(location[0].formatted_address);
					break;
				case '#txtCalculateVia':
					$("#txtCalculateVia").val(location[0].formatted_address);
					break;
				default:
					searchSetCenter(location[0].point.lat,location[0].point.lon,location[0].formatted_address)
			}				
			$( "#dialog-searchmultiple" ).dialog( "close" );
		}
		else {
			var searchresult = "<table>";
			var onclickaction;
			for (var i=0;i<location.length;i++) {
				searchresult += "<tr>";

				switch ($("#txtSearchSource").val()) {
					case '#txtCalculateFrom':
						searchresult += ("<td><a href='#' style='text-decoration:none;' onClick=searchSetJourneyFrom(" + i + ")><span id=searchresult" + i + ">" + location[i].formatted_address + "<span></a></td>");
						break;
					case '#txtCalculateTo':
						searchresult += ("<td><a href='#' style='text-decoration:none;' onClick=searchSetJourneyTo(" + i + ")><span id=searchresult" + i + ">" + location[i].formatted_address + "<span></a></td>");
						break;
					case '#txtCalculateVia':
						searchresult += ("<td><a href='#' style='text-decoration:none;' onClick=searchSetJourneyVia(" + i + ")><span id=searchresult" + i + ">" + location[i].formatted_address + "<span></a></td>");
						break;
					default:
						searchresult += ("<td><a href='#' style='text-decoration:none;' onClick='searchSetCenter(" + location[i].point.lat + "," + location[i].point.lon + ",&quot;" + location[i].formatted_address + "&quot;)'>" + location[i].formatted_address + "</a></td>");
				}				

				searchresult += "</tr>";
			} 
			searchresult += "</table>";
			$("#searchresults").html(searchresult);
			$( "#dialog-searchmultiple" ).dialog( "open" );
		} 
	}
	
	function searchLocation(searchtext,searchsource) {
 		var address = new Object();
 		address.region = searchtext; 
 		address.country = 'uk'; 
 		
 		$("#txtSearchSource").val(searchsource); 
 		$("#txtSearchInline").val(searchtext); 
 		
		geocoderObj.geocode(address);
	}

	function error_callback_reverse(status) {
		switch (status) {
			case 'OVER_QUERY_LIMIT':
					setTimeout(geocodeJourney,constants.GEOCODE_RETRY);
					break;
			default:
				console.log(status);
		}
	}

	function BingReverseGeocode(id,lat,lon) {
		$.getJSON("http://dev.virtualearth.net/REST/v1/Locations/"+lat+","+lon+"?query=&jsonp=?&key=" + BingKey, function(data){
			if (data.resourceSets.length > 0 && data.resourceSets[0].resources.length>0) {
				$("#journeylist").setCell(id,'location',data.resourceSets[0].resources[0].name);
			}
			else {
				$("#journeylist").setCell(id,'location',"no location found");
			}
		});
	}
		
	function BingReverseGeocodeSetInput(inputid,lat,lon) {
		$.getJSON("http://dev.virtualearth.net/REST/v1/Locations/"+lat+","+lon+"?query=&jsonp=?&key=" + BingKey, function(data){
			if (data.resourceSets.length > 0 && data.resourceSets[0].resources.length>0) {
				$(inputid).val(data.resourceSets[0].resources[0].name);
			}
			else {
				$(inputid).val("location not found");
			}
		});
	}
		
	function geocodereverse_return(location) {
		var journeylist = $("#journeylist").getDataIDs();
		for(i=0;i<journeylist.length;i++){
			var id = journeylist[i];
			if ($("#journeylist").getCell(id,'location')=="") {
				$("#journeylist").setCell(id,'location',location[0].formatted_address);
				break;
			}
		}
		geocodeJourney();
	}

	function geocodeJourney() {
		var journeylist = $("#journeylist").getDataIDs();
		for(i=0;i<journeylist.length;i++){
			var id = journeylist[i];
			if (($("#journeylist").getCell(id,'location')=="") && ($("#journeylist").getCell(id,'locationtime')!="")) {
				BingReverseGeocode(id,$("#journeylist").getCell(id,'latitude'),$("#journeylist").getCell(id,'longitude'));
			}
		}	
	}

	function getTrackerJourneys(trackerID) {
		var dateFrom = ($("#journeydate").val() - 946684800000);
		var dateTo = dateFrom + (24*60*60*1000);
		TrackingService.sendJourneyRequest(trackerID,dateFrom/100,dateTo/100);
		$("#journeynothing").hide();
		$("#journeyloading").show();
	}				
	
	//---------------------------------------------------------------------------------------------//
	// UI Layers functions
	//---------------------------------------------------------------------------------------------//
	function toggleLiveResizing () {
		$.each('north,south,west,east'.split(','), function (i, pane) {
			var opts = layoutObj.options[ pane ];
			opts.resizeWhileDragging = !opts.resizeWhileDragging;
		});
	};

	function toggleStateManagement ( skipAlert ) {
		var enable = !layoutObj.options.useStateCookie; // OPPOSITE of current setting
		layoutObj.options.useStateCookie = enable; // toggle option

		if (!enable) { // if disabling state management...
			layoutObj.deleteCookie(); // ...clear cookie so will NOT be found on next refresh
			if (!skipAlert)
				alert( 'This layout will reload as options specify \nwhen the page is refreshed.' );
		}
		else if (!skipAlert)
			alert( 'This layout will save & restore its last state \nwhen the page is refreshed.' );

		// update text on button
		var $Btn = $('#btnToggleState'), text = $Btn.html();
		if (enable)
			$Btn.html( text.replace(/Enable/i, "Disable") );
		else
			$Btn.html( text.replace(/Disable/i, "Enable") );
	};
	
	//---------------------------------------------------------------------------------------------//
	// jqGrid vehiclelist functions
	//---------------------------------------------------------------------------------------------//
	function showSelectedPosition(id) {
		var lat = $("#vehiclelist").getCell(id,constants.TABLE_LATITUDE);
		var lon = $("#vehiclelist").getCell(id,constants.TABLE_LONGITUDE);
		mxnObj.setCenterAndZoom(new mxn.LatLonPoint(lat, lon),(mapFindZoom<=16 || $('#optApi').val()!='mapquest') ?  mapFindZoom: 16);
		$('#clickPosition').html(formatLatitude(lat) + ', ' + formatLongitude(lon));
	}
	
	var eventsMenu = {
		bindings: {
			'showvehicle': function(t) {
				showSelectedPosition(t.id);
			},
			'showjourney': function(t) {
				selectedJourneyID = t.id;
				$("#journeytrackers").val(selectedJourneyID);
				$( "#accordion" ).accordion("activate",1);
				getTrackerJourneys(selectedJourneyID);
			},
			'showjourneyreport': function(t) {
				$('#intUnitIDPDF').val(t.id);
				$('#txtUnitNamePDF').html($("#vehiclelist").getCell(t.id,constants.TABLE_NAME));
				$('#intTrackerIDPDF').val(t.id);
				$("#dialog-pdf").dialog("open");	
			},
			'showeditunit': function(t) {
				$('#intUnitID').val(t.id);
				$('#txtUnitName').val($("#vehiclelist").getCell(t.id,constants.TABLE_NAME));
				$('#optIconType').val($("#vehiclelist").getCell(t.id,constants.TABLE_ICON));
				$("#optIconType").selectmenu();
				$("#dialog-edit").dialog("open");	
			},
			'showcalculatejourney': function(t) {
				cleanDialogCalculate();
				$('#intUnitIDCalculate').val(t.id);
				var calculateLat = $("#vehiclelist").getCell(t.id,constants.TABLE_LATITUDE);
				var calculateLon = $("#vehiclelist").getCell(t.id,constants.TABLE_LONGITUDE);
				$('#intCalculateFromLat').val(calculateLat);
				$('#intCalculateFromLon').val(calculateLon);

				BingReverseGeocodeSetInput('#txtCalculateFrom',calculateLat,calculateLon);
				populateDestinationList();
				$("#dialog-calculate").dialog("open");	
			}
		}
	};
		
	var staticMenu = {
		bindings: {
			'showstatic': function(t) {
				showSelectedStatic(t.id);
			},
			'removestatic': function(t) {
				removeSelectedStatic(t.id);
			},
			'showstaticjourney': function(t) {
				cleanDialogCalculate();
				$('#intStaticIDCalculate').val(t.id);
				var calculateLat = $("#staticlist").getCell(t.id,constants.STATIC_LATITUDE);
				var calculateLon = $("#staticlist").getCell(t.id,constants.STATIC_LONGITUDE);
				$('#intCalculateToLat').val(calculateLat);
				$('#intCalculateToLon').val(calculateLon);

				BingReverseGeocodeSetInput('#txtCalculateTo',calculateLat,calculateLon);
				populateDestinationList();
				$("#dialog-calculate").dialog("open");	
			}
		}
	};
	
	
	$(function() {
		$("#vehiclelist").jqGrid({
			datatype: "local",
			height: '100%',
   			colNames:['ID','','', 'Name', 'Last Updated','Speed','','','Latitude','Longitude','Altitude','OdoKm','Icon','Timestamp','kph',''],
		   	colModel:[
   				{name:'id',index:'id', width:200, hidden:true, sorttype:"int"},
   				{name:'connected',index:'connected', width:20, sorttype:"int", formatter:connectedFormatter, sortable:false},
   				{name:'ignOn',index:'ignOn', width:21, formatter:ignitionFormatter, sortable:false},
   				{name:'name',index:'name', width:130 },
		   		{name:'updated',index:'updated', width:148, sorttype:"date", formatter:lastUpdatedFormatter, sortable:false},
		   		{name:'speed',index:'speed', width:58, align:"left",sorttype:"float", formatter:speedFormatter, sortable:false},	
		   		{name:'direction',index:'direction', width:22, align:"left",sorttype:"int", formatter:directionFormatter, sortable:false},	
		   		{name:'edit',index:'edit', width:22, align:"left",sorttype:"int", hidden:true, formatter:editFormatter, sortable:false},	
		   		{name:'latitude',index:'latitude', width:70, align:"right",sorttype:"float", hidden:true},	
		   		{name:'longitude',index:'longitude', width:70, align:"right",sorttype:"float", hidden:true},	
		   		{name:'altitude',index:'altitude', width:70, align:"right",sorttype:"float", hidden:true},	
		   		{name:'odom',index:'odom', width:70, align:"right",sorttype:"float", hidden:true},	
		   		{name:'icon',index:'icon', width:70, align:"right",sorttype:"int", hidden:true},	
		   		{name:'timestamp',index:'timestamp', width:70, align:"right",sorttype:"float", hidden:true},	
		   		{name:'kph',index:'kph', width:48, align:"left",sorttype:"float", hidden:true, sortable:false},	
		   		{name:'scroll',index:'scroll', width:18, align:"right",sorttype:"float", hidden:true, sortable:false},	
		   	],
		   	rowNum:300,
		   	//pager: '#vehiclepager',
		   	sortname: 'name',
		    viewrecords: true,
		    sortorder: "desc",
		   	caption: "",
			autowidth: false,
			hoverrows: true,
			onSelectRow: function(id,status) {
				showSelectedPosition(id);
			},
			onCellSelect: function(id,iCol,cellcontent,e) {
				// placeholder
			},
			afterInsertRow : function(rowid, rowdata)
			{
				$(this).jqGrid('setRowData', rowid, false, $("#optGridRowNormal").val());			
				$('#' + rowid).contextMenu('vehicleContext',eventsMenu);
			}
		});
	});

	//---------------------------------------------------------------------------------------------//
	// jqGrid formatters
	function directionFormatter(cellvalue, options, rowObject)
	{
		var direction = '';
		if ((cellvalue > 0) && (cellvalue <= constants.DIRECTIONS.length)) {
			direction = constants.DIRECTIONS[cellvalue-1];
		}
		return direction;
	};

	function speedFormatter(cellvalue, options, rowObject)
	{
		return formatSpeed(cellvalue,false);
	};

	function ignitionFormatter(cellvalue, options, rowObject) {
	    if (cellvalue == true) {
      		return "<img src='img/icons/key_red.png' title='Ignition On' />";
		}
	    else { 
      		return "<img src='img/icons/key_grey.png' title='Ignition Off' />";
		}
	}; 
        
	function connectedFormatter(cellvalue, options, rowObject) {
	    if (cellvalue == true) {
      		return "<img src='img/icons/power_on.png' title='Connected' />";
		}
	    else { 
      		return "<img src='img/icons/power_off.png' title='Disconnected' />";
		}
	}; 

	function lastUpdatedFormatter (cellvalue, options, rowObject) {
		if (cellvalue!='') {
			return formatTrackerDate(cellvalue);
		}
		else {
			return '';
		}
	}
	
	function editFormatter(cellvalue, options, rowObject) {
   		return "<img src='img/silk/server_edit' title='Edit' />";
	}; 
	
	//---------------------------------------------------------------------------------------------//
	// jqGrid manipulation functions	
	function changeRowCSS() {
		var vehiclelist = $("#vehiclelist").getDataIDs();
		var gridrownormal = $("#optGridRowNormal").val();
		for(i=0;i<vehiclelist.length;i++){
			var thisrow = "#" + vehiclelist[i];
			$(thisrow).removeClass (function (index, css) {
			    return (css.match (/\bgrid-row-normal-\S+/g) || []).join(' ');
			});
			$(thisrow).addClass(gridrownormal); 
		}
		changeGridContainerCSS();		
	}
	
	function changeGridContainerCSS() {
		var gridrownormal = $("#optGridRowNormal").val();
		
		// backgrounds 
		$("#vehiclelistcontainer").removeClass (function (index, css) {
		    return (css.match (/\bgrid-row-normal-\S+/g) || []).join(' ');
		});
		$("#vehiclelistcontainer").addClass(gridrownormal); 
	}
	
	function changeRowSpeed() {
		var vehiclelist = $("#vehiclelist").getDataIDs();
		for(i=0;i<vehiclelist.length;i++){
			var thisrow = "#" + vehiclelist[i];
			var thisspeed = $("#vehiclelist").getCell(vehiclelist[i],'kph');
			$("#vehiclelist").setCell(vehiclelist[i],'speed',thisspeed);
		}		
	}
	
	function expireDetails() {
		var vehiclelist = $("#vehiclelist").getDataIDs();
		for(i=0;i<vehiclelist.length;i++){
			var thisrow = "#" + vehiclelist[i];
			// Expire speed and direction
			if ($('#optActivityHighlight').val()>0) {
				if (($("#vehiclelist").getCell(vehiclelist[i],'timestamp')) < (lastTrackerTime-(60*10*$('#optActivityHighlight').val())) ) {
					$("#vehiclelist").setCell(vehiclelist[i],'speed','*');
					$("#vehiclelist").setCell(vehiclelist[i],'direction','*');
				}
			}
			// Grey out
			if ($('#optInactiveVehicle').val()>0) {
				if ( ($("#vehiclelist").getCell(vehiclelist[i],'timestamp')) < (lastTrackerTime-(60*10*60*$('#optInactiveVehicle').val())) ) {
					$(thisrow).addClass('grid-row-text-inactive'); 
				}
			}
		}		
	}
	
	//---------------------------------------------------------------------------------------------//
	// jqGrid journeylist functions
	//---------------------------------------------------------------------------------------------//
	$(function() {
		$("#journeylist").jqGrid({
			datatype: "local",
			height: '100%',
   			colNames:['ID','Journey','Tracker','','Time', 'Location','Lat','Long'],
		   	colModel:[
   				{name:'id',index:'id', width:200, hidden:true, sorttype:"int"},
   				{name:'journey',index:'journey', width:180, hidden:true},
   				{name:'tracker',index:'tracker', width:100, hidden:true, sorttype:"int"},
		   		{name:'action',index:'action', width:40, align:"left", sortable:false},	
   				{name:'locationtime',index:'locationtime', width:50, sorttype:"date", formatter:lastFromToFormatter },
		   		{name:'location',index:'location', width:290, sortable:false},
		   		{name:'latitude',index:'latitude', width:200, hidden:true, sortable:false},
		   		{name:'longitude',index:'longitude', width:200, hidden:true, sortable:false},
		   	],
		   	rowNum:100, 
		   	sortname: 'locationtime',
		    viewrecords: true,
		    sortorder: "asc",
		   	caption: "",
		   	grouping:true,
		   	groupingView: {
		   		groupField:['journey'],
		   		groupColumnShow: [false],
		   		groupText : ['<span style="font-weight:bold;">{0}</span>']
		   	},
			autowidth: false,
			hoverrows: true,
			onSelectRow: function(id) {
				var i = Math.round((id/1000)-1); 
				for (j=0;j<journeyObj._journeyList.length;j++) {
					if (typeof(journeyObj._journeyList[j].polyline) != 'undefined') {
						mxnObj.removePolyline(journeyObj._journeyList[j].polyline);
						mxnObj.removeMarker(journeyObj._journeyList[j].endmarker);
						mxnObj.removeMarker(journeyObj._journeyList[j].startmarker);
						journeyObj._journeyList[j].polyline = null;
						journeyObj._journeyList[j].endmarker = null;
						journeyObj._journeyList[j].startmarker = null;
					}					
				}
				
				if ((typeof(journeyObj._journeyList[i].polyline) == 'undefined') ||
					(journeyObj._journeyList[i].polyline == null)) {			
					var journeyPoints = new Array;
					var journeyLength = journeyObj._journeyList[i].trackerMsgs.length-1;
					$.each(journeyObj._journeyList[i].trackerMsgs, function(i, item) {
						journeyPoints.push(new mxn.LatLonPoint(item.gps._latitude,item.gps._longitude));
						if (i==0) {
							mxnObj.setCenter(new mxn.LatLonPoint(item.gps._latitude,item.gps._longitude));
						}
					});
					journeyObj._journeyList[i].polyline = new mxn.Polyline(journeyPoints);
					journeyObj._journeyList[i].polyline.setColor('#FF0000');
					journeyObj._journeyList[i].polyline.setOpacity(0.7);
					journeyObj._journeyList[i].polyline.setWidth(10);
					mxnObj.addPolyline(journeyObj._journeyList[i].polyline);
				
					// start
					var item = journeyObj._journeyList[i].trackerMsgs[0];
					var markerObj = new mxn.Marker(new mxn.LatLonPoint(item.gps._latitude,item.gps._longitude));	
					var markerName = 'img/icons/green_flag.png';
					markerObj.setIcon(markerName,[32,32],[4,10]); 
					markerObj.setLabel(item.analysis.location);
					markerObj.setDisplayText(formatTrackerTime(item.date));
					markerObj.setDisplayClass('displayText');  
					var visibleMarkers = ($("#optShowLabels").val()=="On");
					markerObj.setDisplayVisible(visibleMarkers);
					mxnObj.addMarker(markerObj);
					journeyObj._journeyList[i].startmarker = markerObj;	
					
					// end
					var item = journeyObj._journeyList[i].trackerMsgs[journeyLength];
					var markerObj = new mxn.Marker(new mxn.LatLonPoint(item.gps._latitude,item.gps._longitude));	
					var markerName = 'img/icons/checkered_flag.png';
					markerObj.setIcon(markerName,[32,32],[4,10]);
					markerObj.setLabel(item.analysis.location);
					markerObj.setDisplayText(formatTrackerTime(item.date));
					markerObj.setDisplayClass('displayText');  
					var visibleMarkers = ($("#optShowLabels").val()=="On");
					markerObj.setDisplayVisible(visibleMarkers);
					mxnObj.addMarker(markerObj);
					journeyObj._journeyList[i].endmarker = markerObj;	
				}
				else {
				}
			}
		});
	});

	function lastFromToFormatter (cellvalue, options, rowObject) {
		if (cellvalue!='') {
			return formatTrackerTime(cellvalue);
		}
		else {
			return '';
		}
	}
			

	//---------------------------------------------------------------------------------------------//
	// jqGrid staticlist static functions
	//---------------------------------------------------------------------------------------------//
	$(function() {
		$("#staticlist").jqGrid({
			datatype: "local",
			height: '100%',
   			colNames:['','Icon Ref','Text','Latitude','Longitude'],
		   	colModel:[
		   		{name:'icon',index:'icon', width:50, align:"center", formatter:staticIconFormatter},	
   				{name:'id',index:'id', width:350 },
   				{name:'text',index:'text', width:240, hidden:true},
		   		{name:'latitude',index:'latitude', width:70, align:"right",sorttype:"float", hidden:true},	
		   		{name:'longitude',index:'longitude', width:70, align:"right",sorttype:"float", hidden:true},	
		   	],
		   	rowNum:300,
		   	pager: '#staticpager',
		   	sortname: 'name',
		    viewrecords: true,
		    sortorder: "desc",
		   	caption: "",
			autowidth: false,
			hoverrows: true,
			onSelectRow: function(id,status) {
				showSelectedStatic(id);
			},
			onCellSelect: function(id,iCol,cellcontent,e) {
				//placeholder
			},
			afterInsertRow : function(rowid, rowdata)
			{
				$(this).jqGrid('setRowData', rowid, false);			
				$('#' + rowid).contextMenu('staticContext',staticMenu);
			}
		});
	});

	function staticIconFormatter(cellvalue, options, rowObject) {
	    if (cellvalue != '') {
	    	return "<img src='img/icons/" + cellvalue  + "' title='Job' />";
		}
	    else { 
      		return "";
		}
	}; 
	
	function showSelectedStatic(id) {
		var lat = $("#staticlist").getCell(id,constants.STATIC_LATITUDE);
		var lon = $("#staticlist").getCell(id,constants.STATIC_LONGITUDE);
		mxnObj.setCenterAndZoom(new mxn.LatLonPoint(lat, lon),(mapFindZoom<=16 || $('#optApi').val()!='mapquest') ?  mapFindZoom: 16);
		$('#clickPosition').html(formatLatitude(lat) + ', ' + formatLongitude(lon));
	}
	
	function removeSelectedStatic(id) {
		if (TrackingService.staticitems[id].mapObject != null) {  
			mxnObj.removeMarker(TrackingService.staticitems[id].mapObject);
		    TrackingService.staticitems[id].mapObject = null;
			$('#staticlist').jqGrid('delRowData',id);
			TrackingService.sendRemoveStatic(id);
		}
	}

	//---------------------------------------------------------------------------------------------//
	// jqGrid destinationlist static functions
	//---------------------------------------------------------------------------------------------//
	$(function() {
		$("#destinationlist").jqGrid({
			datatype: "local",
			height: '100%',
   			colNames:['Type','id','Destination','Latitude','Longitude'],
		   	colModel:[
		   		{name:'icon',index:'icon', width:70, align:"center", formatter:destinationIconFormatter},	
   				{name:'id',index:'id', width:50, hidden:true },
   				{name:'text',index:'text', width:250, hidden:false},
		   		{name:'latitude',index:'latitude', width:70, align:"right",sorttype:"float", hidden:true},	
		   		{name:'longitude',index:'longitude', width:70, align:"right",sorttype:"float", hidden:true},	
		   	],
		   	rowNum:300,
		   	sortname: 'name',
		    viewrecords: true,
		    sortorder: "desc",
		   	caption: "",
			autowidth: false,
			hoverrows: true,
			onSelectRow: function(id,status) {
				$("#destinationchosen").val(id);
			},
			afterInsertRow : function(rowid, rowdata) {
				$(this).jqGrid('setRowData', rowid, false, $("#optGridRowNormal").val());			
			}
		});
  	});

	function destinationIconFormatter(cellvalue, options, rowObject) {
	    if (cellvalue != '') {
	    	return "<img src='" + cellvalue  + "' title='Vehicle' />";
		}
	    else { 
      		return "";
		}
	}; 

	function cleanDialogCalculate() {
		$('#txtCalculateFrom').val("");
		$('#intCalculateFromLat').val("");
		$('#intCalculateFromLon').val("");
		$('#txtCalculateTo').val("");
		$('#intCalculateToLat').val("");
		$('#intCalculateToLon').val("");
		$('#txtCalculateVia').val("");
		$('#intCalculateViaLat').val("");
		$('#intCalculateViaLon').val("");
	}

	function populateDestinationList() {

		$('#destinationlist').jqGrid('clearGridData');

		$.each(TrackingService.staticitems, function(i, item) {
			var destinationObj = new Object;
			destinationObj.icon = '/img/icons/' + item.icon;
			destinationObj.id = item.id;
			destinationObj.text = item.id;
			destinationObj.latitude = item.latitude;
			destinationObj.longitude = item.longitude;
			jQuery("#destinationlist").jqGrid('addRowData',item.id,destinationObj);

		});

		$.each(TrackingService.accounts, function(i, item) {
			var destinationObj = new Object;
			destinationObj.icon = getMarkerIcon(item.icon,0);
			destinationObj.id = item.id;
			destinationObj.text = item.name;
			destinationObj.latitude = item.latitude;
			destinationObj.longitude = item.longitude;
			jQuery("#destinationlist").jqGrid('addRowData',item.id,destinationObj);

		});
		
	}

	//---------------------------------------------------------------------------------------------//
	// UI Event handling functions
	//---------------------------------------------------------------------------------------------//
	$(window).load(function() {
		create_map();
	    layoutObj.resizeAll();
	});
	
	// TODO this uses fg group menu - replace with jquery-ui menu when 1.9 is released
	function menuItemChoice(msg) {
		switch(msg) {
			case 'connection':
				if (websocketsActive) {							   
					TrackingService.closeConnection();
					setConnectStatus(false);
					websocketsActive = false;
				}
				else {
					$("#dialog-logon").dialog("open");
					$
				}
				break;
			case 'reset':
				layoutObj.loadState(stateResetSettings, true);
				break;
			case 'settings':
				// The server returns settings on logon, but will do so if none have been set, hence we need defaults
				if (!settingsReturned) {
					useDefaultSettings();
				}
				$( "#dialog-settings" ).dialog( "open" );
				break;
			case 'about':
				BingReverseGeocode(1,51.5,-0.116667)
				break;
			case 'test':
				//resize_map();
				layoutObj.resizeAll()
				break;
		}
	}
	
	//---------------------------------------------------------------------------------------------//
	// Tracking callback handling: 
	//---------------------------------------------------------------------------------------------//
	function addOrUpdateVehicle(data) {
		if ($("#vehiclelist").jqGrid('getInd',data.id)) { 
			$("#vehiclelist").setCell(data.id,'name',this.name);
		}
		else {
			var vehicleObj = new Object;
			vehicleObj.id = data.id;
			vehicleObj.connected = data.connected;					
			vehicleObj.name = data.name;
			vehicleObj.updated = '';
			vehicleObj.speed = '';
			vehicleObj.latitude = '';
			vehicleObj.longitude = '';
			vehicleObj.altitude = '';
			vehicleObj.odom = '';			
			vehicleObj.icon = data.icon;
			jQuery("#vehiclelist").jqGrid('addRowData',data.id,vehicleObj);
		}
	}
	
	function addJourney(i,data) {
		var rowid = (i*1000)+1000;
		i++; 
		var journeyName = 'Journey ' + ((i < 10 ? '0' : '') + i) + " (" + formatDistance(data.disKM) + ")";
		var journeyObjFrom = new Object;
		journeyObjFrom.id = rowid++;
		journeyObjFrom.journey = journeyName;
		journeyObjFrom.tracker = data.id;
		journeyObjFrom.action = 'From:';
		journeyObjFrom.locationtime = data.dateFrom;					
		journeyObjFrom.location = data.locFrom;
		journeyObjFrom.latitude = data.latFrom;
		journeyObjFrom.longitude = data.lonFrom;
		jQuery("#journeylist").jqGrid('addRowData',rowid,journeyObjFrom);		
		
		var journeyObjTo = new Object;
		journeyObjTo.id = rowid++;
		journeyObjTo.journey = journeyName;
		journeyObjTo.tracker = data.id;
		journeyObjTo.action = 'To:';		
		journeyObjTo.locationtime = data.dateTo;					
		journeyObjTo.location = data.locTo;
		journeyObjTo.latitude = data.latTo;
		journeyObjTo.longitude = data.lonTo;
		jQuery("#journeylist").jqGrid('addRowData',rowid,journeyObjTo);		
	}
	
	function setConnectStatus(connected) {
		if (connected) {
			$("#connectionStatus").css("background-color","#22ee11");
			$("#connectionStatus").text("Connected");
			//$('#btnConnected').button('option', 'label', 'Disconnect');
		}
		else {
			$("#connectionStatus").css("background-color","red");
			$("#connectionStatus").text("Disconnected");
			//$('#btnConnected').button('option', 'label', 'Connect');
		}
	}
		
	function addStaticIcons(data) {
		$.each(data.addIcon, function(i, item) {
			if ($("#staticlist").jqGrid('getInd',item.id)) { 
				$("#staticlist").setCell(data.id,'name',item.text);
			}
			else {
				var staticObj = new Object;
				staticObj.icon = item.icon;
				staticObj.id = item.id;
				staticObj.text = item.text;
				staticObj.latitude = item.lat;
				staticObj.longitude = item.lon;
				jQuery("#staticlist").jqGrid('addRowData',item.id,staticObj);

				// static
				var markerObj = new mxn.Marker(new mxn.LatLonPoint(item.lat,item.lon));	
				var markerName = 'img/icons/' + item.icon;
				markerObj.setIcon(markerName,[32,32],[4,10]);
				markerObj.setLabel(item.id);
				markerObj.setDisplayText(item.id);
				markerObj.setDisplayClass('displayText');  
				var visibleMarkers = ($("#optShowLabels").val()=="On");
				markerObj.setDisplayVisible(visibleMarkers);
				mxnObj.addMarker(markerObj);
				TrackingService.staticitems[item.id].mapObject = markerObj;
			}
		});		
	}
		
	function removeStaticIcon(data) {
		$.each(data.delIcon, function(i, item) {
			removeSelectedStatic(item);
		}); 
	}
		
	function trackingCallback (msg, data) {
		switch(msg) {
			case constants.LOGON_TRUE:
				setConnectStatus(true);
				websocketsActive = true;
				$('#iconSignal').everyTime(constants.DEFAULT_UPDATETICK,function () {
					 $("#iconSignal").toggle("slow");
					 expireDetails();
				})
				// also do it once at 2 seconds so the grid doesn't hang around not updated too long
				$('#iconSignal').oneTime(constants.DEFAULT_INITIALTICK,function () {
					 expireDetails();
				})
				break;
			case constants.LOGON_FALSE:
				$( "#dialog-confirm" ).dialog( "open" );
				websocketsActive = false;
				break;
			case constants.LOGON_INVALID:
				$("#invalidmsg").attr('class', 'ui-state-error');
				$("#invalidmsg").html("Invalid Username/Password");
				$( "#dialog-logon" ).dialog( "open" );
				websocketsActive = false;
				break;
			case constants.WEBSOCKET_ERROR:
				console.log(data);
				break;
			case constants.WEBSOCKET_CLOSED:
				setConnectStatus(false);
				if (websocketsActive) { // that is we were logged on, not just a failed login
					$( "#dialog-logon" ).dialog( "open" );
				}
				websocketsActive = false;
				break;
			case constants.ACCOUNTS:
				$.each(data, function () {
					addOrUpdateVehicle(this);			   
				});			
				$("#vehiclelist").sortGrid('name',false);														
				break;
			case constants.ACCOUNT:
				addOrUpdateVehicle(data);			   
				break; 
			case constants.PLACEHOLDER:
				data.name = 'n/a';
				addOrUpdateVehicle(data);			   
				break;
			case constants.TRACKER:
				if (data.dateTime > lastTrackerTime) {
					lastTrackerTime = data.dateTime;
				}
				var ignitionOn = (data.ignOn) ? 1 : 0;
				jQuery("#vehiclelist").setCell(data.id,'ignOn',ignitionOn);
				jQuery("#vehiclelist").setCell(data.id,'updated',data.dateTime);
				var quadrant = 0;
				if (data.gps.speedKm>0) {
					quadrant = Math.floor((data.gps.direction+22.5)/45) + 1;
				}
				$("#vehiclelist").setCell(data.id,'speed',data.gps.speedKm);
				$("#vehiclelist").setCell(data.id,'kph',data.gps.speedKm);
				$("#vehiclelist").setCell(data.id,'direction',quadrant);
				$("#vehiclelist").setCell(data.id,'latitude',data.gps.latitude);
				$("#vehiclelist").setCell(data.id,'longitude',data.gps.longitude);
				$("#vehiclelist").setCell(data.id,'altitude',data.gps.altitude);
				$("#vehiclelist").setCell(data.id,'odom',data.odoM);
				$("#vehiclelist").setCell(data.id,'timestamp',data.dateTime);
				 
				var activityhighlight = parseInt($("#optActivityHighlight").val());
				var gridrownormal = $("#optGridRowNormal").val();
				var gridrowactive = $("#optGridRowActive").val();
				
				var thisRow = "#" + data.id;
				$(thisRow).removeClass (function (index, css) {
				    return (css.match (/\bgrid-row-text-inactive-\S+/g) || []).join(' ');
				});
				if (activityhighlight > 0) {					
					$(thisRow).removeClass (function (index, css) {
				    	return (css.match (/\bgrid-row-normal-\S+/g) || []).join(' ');
					});
					$(thisRow).addClass(gridrowactive);
								
					$(this).oneTime(activityhighlight, function() {
						$(thisRow).removeClass (function (index, css) {
					    	return (css.match (/\bgrid-row-active-\S+/g) || []).join(' ');
						});
						$(thisRow).addClass(gridrownormal);
					});
				}				
				showMarker(data);
				break;
			case constants.UNSUPPORTED:
				$( "#dialog-about" ).dialog( "open" );		
				break;
			case constants.CONNECTED:
				jQuery("#vehiclelist").setCell(data.connected.id,'connected',1);
				connectMarker(data);
				break;
			case constants.DISCONNECTED:
				jQuery("#vehiclelist").setCell(data.disconnected.id,'connected',0);
				disconnectMarker(data);
				break;
			case constants.KEYVAL:	
				retrieveSettings(data);
				break;
			case constants.JOURNEYLIST:
				clearJourneys();
				journeyObj = data.journeys;
				var journeyFound = false;
				$('#journeylist').jqGrid('clearGridData');
				$.each(data.journeys._journeyList, function(i, item) {
					data.journeys._journeyList[i].polyline = null;
					data.journeys._journeyList[i].endmarker = null;
					addJourney(i,item);
					journeyFound = true;
				});
				$('#journeylist').jqGrid('sortGrid', 'tracker', true);
				$("#journeyloading").hide();
				if (!journeyFound) {
					$("#journeynothing").show();				
				}
				geocodeJourney();			
				setTimeout(geocodeJourney(),200); //Run it a secound time, just to be sure there are no blanks.
				break;
			case constants.REPORTCALLBACK:
				currentPDF = data.reportCallback.message;
				if ($('#boolDisplayPDF').attr('checked')) {
					$("#dialog-pdfready").dialog("open");				
				}
				break;
			case constants.ADDICON:
				addStaticIcons(data);
				break;
			case constants.DELICON:;
				removeStaticIcon(data);
				break;
			default:
				;
		}
	}

	function trackingServiceInit(sessionKick) {
		TrackingService.init(username, password, sessionKick, '[var.websocketURL]', trackingCallback);
	}
		
	function checkLength( o, n, min, max ) {
		if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			return false;
		} else {
			return true;
		}
	}
		
	//---------------------------------------------------------------------------------------------//
	// State handling functions
	//---------------------------------------------------------------------------------------------//
	function setLogonCookie(username,password) {
		$.post("ajax/setcookie.ajax.php",
		{
			thisUsername: username,
			thisPassword: password
		},
		function(data){
			//placeholder
		},
		"json"
		);
	}

	//---------------------------------------------------------------------------------------------//
	// cookies, these are needed because we may retrieve server settings after we've actioned methods using these values on the page
	if($.cookie("css")) {
		var jqueryuitheme = $.cookie("css");
		$("#jqueryuitheme").attr("href",jqueryuitheme);
	}

	if($.cookie("showlabels")) {
		var showlabels = $.cookie("showlabels");
		$("#optShowLabels").val(showlabels);
	}
	
	//---------------------------------------------------------------------------------------------//
	// Keystore settings handling
	// Defaults are required because the server will not send us a keystore is we havn't saved anything
	
	function initialiseSettingsSelects() {
		$("#optGridRowNormal").selectmenu();
		$("#optGridRowActive").selectmenu();
		$("#optActivityHighlight").selectmenu();
		$("#optInactiveVehicle").selectmenu();	
		$("#optShowLabels").selectmenu();
		$("#optThemePicker").selectmenu();
		$("#optMarkerType").selectmenu();
		$("#optSpeedUnits").selectmenu();
		$("#optShowTraffic").selectmenu();
		$("#optShowSeamap").selectmenu();
		$("#optFindZoom").selectmenu();
		$("#optShowInactive").selectmenu(); 
	}

	function useDefaultSettings() {
		$("#optGridRowNormal").val(constants.DEFAULT_GRIDROWNORMAL);
		$("#optGridRowActive").val(constants.DEFAULT_GRIDROWACTIVE);
		$("#optActivityHighlight").val(constants.DEFAULT_ACTIVITYHIGHLIGHT);
		$("#optInactiveVehicle").val(constants.DEFAULT_INACTIVEVEHICLE);
		$("#optShowLabels").val(constants.DEFAULT_SHOWLABELS);
		$("#optMarkerType").val(constants.DEFAULT_MARKER);
		$("#optSpeedUnits").val(constants.DEFAULT_SPEEDUNIT);
		$("#optShowTraffic").val(constants.DEFAULT_SHOWTRAFFIC);
		$("#optShowSeamap").val(constants.DEFAULT_SHOWSEAMAP);
		$("#optFindZoom").val(constants.DEFAULT_FINDZOOM);
		$("#optShowInactive").val(constants.DEFAULT_SHOWINACTIVE);
					
		// applied afterwards or else does not work. 
		initialiseSettingsSelects();
	}
		
	function retrieveSettings(data) {
		$("#optGridRowNormal").val((typeof(data.keyval.gridrownormal) != 'undefined' ) ? data.keyval.gridrownormal : constants.DEFAULT_GRIDROWNORMAL);
		$("#optGridRowActive").val((typeof(data.keyval.gridrowactive) != 'undefined' ) ? data.keyval.gridrowactive : constants.DEFAULT_GRIDROWACTIVE);
		$("#optActivityHighlight").val((typeof(data.keyval.activityhighlight) != 'undefined' ) ? data.keyval.activityhighlight : constants.DEFAULT_ACTIVITYHIGHLIGHT);
		$("#optInactiveVehicle").val((typeof(data.keyval.inactivevehicle) != 'undefined' ) ? data.keyval.inactivevehicle : constants.DEFAULT_INACTIVEVEHICLE);
		$("#optShowLabels").val((typeof(data.keyval.showlabels) != 'undefined' ) ? data.keyval.showlabels : constants.DEFAULT_SHOWLABELS);
		$("#optMarkerType").val((typeof(data.keyval.markers) != 'undefined' ) ? data.keyval.markers : constants.DEFAULT_MARKER);
		$("#optSpeedUnits").val((typeof(data.keyval.speedunit) != 'undefined' ) ? data.keyval.speedunit : constants.DEFAULT_SPEEDUNIT);
		$("#optShowTraffic").val((typeof(data.keyval.traffic) != 'undefined' ) ? data.keyval.traffic : constants.DEFAULT_SHOWTRAFFIC);
		$("#optShowSeamap").val((typeof(data.keyval.seamap) != 'undefined' ) ? data.keyval.seamap : constants.DEFAULT_SHOWSEAMAP);
		$("#optFindZoom").val((typeof(data.keyval.findzoom) != 'undefined' ) ? data.keyval.findzoom : constants.DEFAULT_FINDZOOM);
		$("#optShowInactive").val((typeof(data.keyval.showinactive) != 'undefined' ) ? data.keyval.showinactive : constants.DEFAULT_SHOWINACTIVE);

		$("#txtEmailPDF").val((typeof(data.keyval.pdfemail) != 'undefined' ) ? data.keyval.pdfemail : "");
										
		mapHomeCenter = new mxn.LatLonPoint(data.keyval.lat, data.keyval.lon);	// map home default
		mapHomeZoom = parseFloat(data.keyval.magnify);			// map zoom default
		mapFindZoom = parseInt(data.keyval.findzoom);
		mxnObj.setCenterAndZoom(mapHomeCenter, mapHomeZoom);

		// applied afterwards or else does not work.  This is a shame because we need them 
		initialiseSettingsSelects();
		
		// traffic etc
		mxnObj.applyDataLayer('trafficLayer',($("#optShowTraffic").val()=='On'));
		mxnObj.applyDataLayer('seamark',($("#optShowSeamap").val()=='On'));

		// grid backgrounds
		changeGridContainerCSS();

		// flag settings returned
		settingsReturned = true;
	}

	//gridrownormal,gridrowactive,activityhighlight,inactivevehicle,theme,lat,lon,magnify,vehiclelabels
	function saveSettings() {
		// save some data to cookie so we can load immediatly
		$.cookie("showlabels",$("#optShowLabels").val(), {expires: 365, path: '/'});

		TrackingService.sendKeyValuePairs(
			$("#optGridRowNormal").val(),
			$("#optGridRowActive").val(),
			$("#optActivityHighlight").val(),
			$("#optInactiveVehicle").val(),
			$("#optThemePicker").val(),
			mapHomeCenter.lat.toString(),
			mapHomeCenter.lon.toString(),
			mapHomeZoom.toString(),
			$("#optShowLabels").val(),
			$("#optMarkerType").val(),
			$("#optSpeedUnits").val(),
			$("#optShowTraffic").val(),
			$("#optShowSeamap").val(),
			$("#optFindZoom").val(),
			$("#optShowInactive").val()
		);
		
		mapFindZoom = parseInt($("#optFindZoom").val());
	}

	function renderTestPolyline() {
		  var points = [];	
       	  point = new mxn.LatLonPoint(50.0, -0.5)
          points.push(point);
          point = new mxn.LatLonPoint(51.0, -0.5)
          points.push(point);
          point = new mxn.LatLonPoint(51.0, -1.5)
          points.push(point);
          point = new mxn.LatLonPoint(50.0, -1.5)
          points.push(point);
          var polygon = new mxn.Polyline(points);
          mxnObj.addPolyline(polygon);
	}

	// 
	//---------------------------------------------------------------------------------------------//
	function calcRoute(fromLocation,toLocation,viaLocation) {
		var request = {                                                  // Instantiate a DirectionsRequest object                                                           
			origin:fromLocation, 
			destination:toLocation,
			travelMode: google.maps.DirectionsTravelMode.DRIVING
		}
		
		if (viaLocation!="") {
			request.waypoints = [{
				location:viaLocation,
				stopover:false
			}];
		}
		
		directionsServiceObj.route(request, function(result, status) {     
			if (status == google.maps.DirectionsStatus.OK) {
				$('#dialog-calculate').dialog( "close" );
				$( "#accordion" ).accordion("activate",2);
				directionsDisplayObj.setMap(mxnObj.maps.googlev3);
        		directionsDisplayObj.setDirections(result);                  // draw the routes
        		var startPoint = null;
        		if ($('#optApi').val()!='google') {
					var points = new Array();
					for (j=0;j<result.routes[0].overview_path.length;j++) {
						var pos = result.routes[0].overview_path[j];

						// name of keys does not seem to be consistent - this ensure that we take item 0 then 1 without knowing the name
						var coords = new Array;
						for(var key in pos) {
    						coords.push(pos[key]);
						}
						var thisPoint = new mxn.LatLonPoint(coords[0],coords[1]);

						points.push(thisPoint);
						if (j==0) {
							startPoint = thisPoint;	
						}
					}				
					routePoly = new mxn.Polyline(points);
					routePoly.setWidth(7);
					routePoly.setColor('#FF0080');
					routePoly.setOpacity(0.8);

					mxnObj.addPolyline(routePoly);          					
					mxnObj.setCenter(startPoint);
				}
			}
			else {
				alert("Error requesting directions");
			}
		});
	}

	$(document).ready(function () {
		//---------------------------------------------------------------------------------//						
		// layout
		layoutObj = $('body').layout({
			//	enable showOverflow on west-pane so CSS popups will overlap north pane
			west__showOverflowOnHover: true
			//	reference only - these options are NOT required because 'true' is the default
			,	closable:				true	// pane can open & close
			,	resizable:				true	// when open, pane can be resized 
			,	slidable:				true	// when closed, pane can 'slide' open over other panes - closes on mouse-out

			,   north__size:            45      //"auto"
	        ,   north__initClosed:      false
	        ,   north__initHidden:      false
        	,   south__initClosed:      true
            ,   south__initHidden:      true
	        ,   west__initClosed:       true
        	,   west__initHidden:       true
	        ,   east__initClosed:       false
        	,   east__initHidden:       false


			//	some resizing/toggling settings
			,	north__slidable:		false	// OVERRIDE the pane-default of 'slidable=true'
			,	north__togglerLength_closed: '100%'	// toggle-button is full-width of resizer-bar
			,	north__spacing_closed:	20		// big resizer-bar when open (zero height)
			,	north__spacing_open:	0		// no resizer-bar when open (zero height)
			,	north__resizable:		false	// OVERRIDE the pane-default of 'resizable=true'
			,	south__resizable:		false	// OVERRIDE the pane-default of 'resizable=true'
			,	south__spacing_open:	0		// no resizer-bar when open (zero height)
			,	south__spacing_closed:	0		// big resizer-bar when open (zero height)
			//	some pane-size settings
			,	west__minSize:			0
			,	east__size:				450
			,	east__minSize:			20
			,	east__maxSize:			Math.floor(screen.availWidth / 2) // 1/2 screen width
			,	center__minWidth:		100
			,	useStateCookie:			false
			,
			// added event
			center__onresize: function(pane, $Pane, paneState) {
			    var width  = paneState.innerWidth; 
			    var height = paneState.innerHeight; 
				$('.mapstraction').height(height);
				$('.mapstraction').width(width);
				mxnObj.resizeTo(width,height);
				$("#accordion").accordion("resize");
				
				$("#vehiclelist").jqGrid('setGridWidth',calculateGridWidth());
				$("#journeylist").jqGrid('setGridWidth',calculateGridWidth());
			}
		});

		// if there is no state-cookie, then DISABLE state management initially
		var cookieExists = false;
		for (var key in layoutObj.getCookie()) {
			cookieExists = true;
			break
		}
		if (!cookieExists) toggleStateManagement( true );

		// add event to the 'Toggle South' buttons in Center AND South panes dynamically...
		layoutObj.addToggleBtn('.south-toggler', 'south');

		// 'Reset State' button requires updated functionality in rc29.15
		if ($.layout.revision && $.layout.revision >= 0.032915) {
			$('#btnReset').show();
		}
				
		//---------------------------------------------------------------------------------//						
		// websockets
    	if(!("WebSocket" in window) && !("MozWebSocket" in window))
		{
			window.location = "browsers/index.html";
		};
		
		//---------------------------------------------------------------------------------//						
		// logon dialog
		function actionDialogLogon() {
			var bValid = true;
			$("#txtPassword").removeClass( "ui-state-error" );
			$("#txtUsername").removeClass( "ui-state-error" );

			bValid = bValid && checkLength( $("#txtUsername"), "username", 3, 16 );
			bValid = bValid && checkLength( $("#txtPassword"), "password", 3, 16 );

			username = $("#txtUsername").val(); 
			password = $("#txtPassword").val(); 
					
			if ( bValid ) {
				trackingServiceInit(false);
				$("#dialog-logon").dialog("close");
			}
			if ($("#bitSaveDetails").is(':checked')) {
				setLogonCookie(username,password);
			}
			else {
				setLogonCookie('','');
			}
		}
		
		$( "#dialog-logon" ).dialog({
			autoOpen: false,
			height: 400,
			width: 500,
			modal: true,
			buttons: {
				"Login": function() {
					actionDialogLogon();
				}
			},
			open: function() {
				$("#txtUsername").focus();
			},
			close: function() {
				$("#invalidmsg").html("&nbsp;");
				$("#invalidmsg").attr('class', '');
			}
		});
		
		//---------------------------------------------------------------------------------//						
		// confirm dialog
		$( "#dialog-confirm" ).dialog({
			autoOpen: false,
			resizable: false,
			height:160,
			modal: true,
			buttons: {
				"Yes": function() {
					trackingServiceInit(true);
					$( this ).dialog( "close" );
				},
				"No": function() {
					$( this ).dialog( "close" );
				}
			}
		});


		//---------------------------------------------------------------------------------//						
		// search failed dialog
		$( "#dialog-searchfailed" ).dialog({
			autoOpen: false,
			resizable: false,
			height:160,
			modal: true,
			buttons: {
				"OK": function() {
					$( this ).dialog( "close" );
				}
			}
		});

		//---------------------------------------------------------------------------------//						
		// search multiple dialog
		$( "#dialog-searchmultiple" ).dialog({
			autoOpen: false,
			resizable: false,
			width:500,
			height:360,
			modal: true,
			buttons: {
				"Close": function() {
					$( this ).dialog( "close" );
				}
			}
		});

		//---------------------------------------------------------------------------------//						
		// about dialog
		$( "#dialog-about" ).dialog({
			autoOpen: false,
			resizable: false,
			height:350,
			width:500,
			modal: true,
			buttons: {
				"OK": function() {
					$( this ).dialog( "close" );
				}
			}
		});

		//---------------------------------------------------------------------------------//						
		// settings dialog
		$('#btnSetHome').button();
		$('#btnSetHome').click( function() {
			mapHomeCenter = mxnObj.getCenter();
			mapHomeZoom = mxnObj.getZoom();
			$("#msgHomePositionSet").html(" Home Position Set ");
		})
		$( "#tbsSettings" ).tabs();

		$( "#dialog-settings" ).dialog({
			autoOpen: false,
			resizable: false,
			height:430,
			width:400,
			modal: true,
			buttons: {
				"Close": function() {
					saveSettings();
					changeRowCSS();
					$("#msgHomePositionSet").html("");
					$( this ).dialog( "close" );
				}
			}
		});

		//---------------------------------------------------------------------------------//						
		// unsupported dialog
		$( "#dialog-unsupported" ).dialog({
			autoOpen: false,
			resizable: false,
			height:180,
			modal: true,
			buttons: {
				"OK": function() {
					$( this ).dialog( "close" );
				}
			}
		});
		
		//---------------------------------------------------------------------------------//						
		// pdf dialog
		$( "#dialog-pdf" ).dialog({
			autoOpen: false,
			resizable: false,
			height:500,
			width:520,
			modal: true,
			buttons: {
				"OK": function() {
					var trackerID = $("#intUnitIDPDF").val();
					var dateFrom = ($("#pdfdatefrom").val() - 946684800000);
					var dateTo = ($("#pdfdateto").val() - 946684800000) + 86400000; // one full day
					var unit = $("#txtDistanceUnitPDF").val();
					var display = $('#boolDisplayPDF').attr('checked');
					var email = $.trim($("#txtEmailPDF").val());					
					
					// validation
					var boolOK = true;
					var dayCount = dateTo - dateFrom;
					if ((dayCount < 0) || (dayCount > 604800000)) {
						boolOK = false;
					}

					if ((!display) && (email=='')) {
						boolOK = false;
					}

					// all in one place
					if (boolOK) {

						TrackingService.sendKeyValuePdfEmail(email);
						TrackingService.sendPDFRequest(trackerID,dateFrom/100,dateTo/100,unit,email);
						$(this).dialog( "close" );						
					}
					else {
						alert("You must select between 1 and 7 consecutive days, and choose to email or view your report.");
					}
				},
				"Cancel": function() {
					$(this).dialog( "close" );
				}
			}
		});

		//---------------------------------------------------------------------------------//						
		// edit dialog
		$( "#dialog-edit" ).dialog({
			autoOpen: false,
			resizable: false,
			height:180,
			modal: true,
			buttons: {
				"OK": function() {
					var id = $('#intUnitID').val();
					$("#vehiclelist").setCell(id,constants.TABLE_NAME,$('#txtUnitName').val());
					$("#vehiclelist").setCell(id,constants.TABLE_ICON,$('#optIconType').val());
					reloadMarkers();
					TrackingService.sendAccount(
						id.toString(),
						$('#txtUnitName').val(),
						$("#optIconType").val().toString()
					);		
					$(this).dialog( "close" );
				}
			}
		});
		
		//---------------------------------------------------------------------------------//						
		// calculate dialog
		$( "#dialog-calculate" ).dialog({
			autoOpen: false,
			resizable: false,
			height:210,
			width:500,
			modal: true,
			buttons: {
				"OK": function() {
					var id = $('#intUnitID').val();
					if ($('#intCalculateFromLat').val()=="") {
						var fromLocation = $('#txtCalculateFrom').val();
					}
					else {
						var fromLocation = new google.maps.LatLng($('#intCalculateFromLat').val(), $('#intCalculateFromLon').val());
					}

					if ($('#intCalculateToLat').val()=="") {
						var toLocation = $('#txtCalculateTo').val();
					}
					else {
						var toLocation = new google.maps.LatLng($('#intCalculateToLat').val(), $('#intCalculateToLon').val());
					}

					if ($('#intCalculateViaLat').val()=="") {
						var viaLocation = $('#txtCalculateVia').val();
					}
					else {
						var viaLocation = new google.maps.LatLng($('#intCalculateViaLat').val(), $('#intCalculateViaLon').val());
					}

					calcRoute(fromLocation,toLocation,viaLocation);
					//$(this).dialog( "close" );
				},
				"Cancel": function() {
					$(this).dialog( "close" );
				}
			}
		});

		//---------------------------------------------------------------------------------//						
		// location dialog
		$( "#dialog-location" ).dialog({
			autoOpen: false,
			resizable: false,
			height:500,
			width:400,
			modal: true,
			buttons: {
				"OK": function() {
					var destinationid = jQuery("#destinationchosen").val();
					switch ($("#destinationpick").val()) {
						case 'from':
							$("#txtCalculateFrom").val($("#destinationlist").getCell(destinationid,'text'));
							$("#intCalculateFromLat").val($("#destinationlist").getCell(destinationid,'latitude'));
							$("#intCalculateFromLon").val($("#destinationlist").getCell(destinationid,'longitude'));
							break;
						case 'to':
							$("#txtCalculateTo").val($("#destinationlist").getCell(destinationid,'text'));
							$("#intCalculateToLat").val($("#destinationlist").getCell(destinationid,'latitude'));
							$("#intCalculateToLon").val($("#destinationlist").getCell(destinationid,'longitude'));
							break;
						case 'via':
							$("#txtCalculateVia").val($("#destinationlist").getCell(destinationid,'text'));
							$("#intCalculateViaLat").val($("#destinationlist").getCell(destinationid,'latitude'));
							$("#intCalculateViaLon").val($("#destinationlist").getCell(destinationid,'longitude'));
							break;
					}

					$(this).dialog( "close" );
				},
				"Cancel": function() {
					$(this).dialog( "close" );
				}
			}
		});

		//---------------------------------------------------------------------------------//						
		// pdf dialog
		$( "#dialog-pdfready" ).dialog({
			autoOpen: false,
			resizable: false,
			height:150,
			modal: true,
			buttons: {
				"View": function() {	
					$(this).dialog( "close" );
					window.open(currentPDF,"PDF");			
				},
				"Cancel": function() {	
					$(this).dialog( "close" );
				}
			}
		});
		
		// login dialog open
		$("#dialog-logon").dialog( "open" );		
				
		//---------------------------------------------------------------------------------//						
		// Menu UI Objects
		//$("#datepicker").datetimepicker();
		
		$('#flat').menu({ 
			content: $('#flat').next().html(), // grab content from this page
			showSpeed: 400 
		});
				
		//---------------------------------------------------------------------------------//						
		// UI Objects and Events
		$("#optApi").selectmenu();	
		$('#optApi').change(function(){
			clearMarkers();
			var mapapi = $("#optApi").val();
			$.cookie("map",mapapi, {expires: 365, path: '/'});
			changeMapAPI();
			reloadMarkers();

			// mapstraction is flakey for microsoft7 when swapping out to something else. 
			// Forcing a click reset the map reliably
			$('#btnHome').click();
		});
		
		$('#optThemePicker').change(function(){
			var jqueryuitheme = $("#optThemePicker").val();
			$.cookie("css",jqueryuitheme, {expires: 365, path: '/'});
			$("#jqueryuitheme").attr({"href": jqueryuitheme});
		});	
		
		$('#optShowLabels').change(function(){
			reloadMarkers();
		});
		
		$('#optMarkerType').change(function(){
			reloadMarkers();
		});
		
		$('#optSpeedUnits').change(function(){
			changeRowSpeed();
		});
		
		$('#optShowTraffic').change(function(){
			mxnObj.applyDataLayer('trafficLayer',($("#optShowTraffic").val()=='On'));
		});
				
		$('#optShowSeamap').change(function(){
			mxnObj.applyDataLayer('seamark',($("#optShowSeamap").val()=='On'));
		});
		
		$('#optShowInactive').change(function(){
			clearMarkers();
			reloadMarkers();
		});
				
		$('#btnHome').button({
		});
		$('#btnHome').click( function() {
			mxnObj.setCenterAndZoom(mapHomeCenter, mapHomeZoom);
		});

		$('#btnRun').button({
			disabled: true
		});
		
		$('#btnAddStatic').button({
		});
		$('#btnAddStatic').click( function() {
		});		
		
		$('#btnSearch').button({
		});
		$('#btnSearch').click( function() {
			searchLocation($("#txtSearch").val(),'');
		});
		$("#txtSearch").keyup(function(event){
			if(event.keyCode == 13){
				$("#btnSearch").click();
			}
		});
		
		//$('#btnPDF').button({
		//	disabled: true
		//});
		//$('#btnPDF').click( function() {
		//	if (currentPDF != null) {
		//		window.open(currentPDF,"PDF");			
		//	}
		//	else {
		//		alert('No report available');
		//	}
		//});
		
    				
		$('#btnSearchInline').button({
		});
		$('#btnSearchInline').click( function() {
			searchLocation($("#txtSearchInline").val(),'');
		});		
		$("#txtSearchInline").keyup(function(event){
			if(event.keyCode == 13){
				$("#btnSearchInline").click();
			}
		});
		
		$('#btnHome').click( function() {
			mxnObj.setCenterAndZoom(mapHomeCenter, mapHomeZoom);
		});
		
		$('#btnReset').button();
		
		$('#btnDebug').click( function() {
			mxnObj.setCenterAndZoom(mapHomeCenter, mapHomeZoom);
		});
		
		// logon dialog - press return on first field
		$("#txtUsername").keyup(function(event){
			if(event.keyCode == 13){
				actionDialogLogon();
			}
		});
				
		$("#txtPassword").keyup(function(event){
			if(event.keyCode == 13){
				actionDialogLogon();
			}
		});

		$( "#journeydate" ).datepicker({
			dateFormat: '@',
			onSelect: function(dateText, inst) {
				getTrackerJourneys($("#journeytrackers").val());
			}
		});
		$( "#journeyloading" ).hide();	
		$( "#journeynothing" ).hide();	
		
		$("#journeytrackers").selectmenu();
		$("#journeytrackers").change( function() {
			getTrackerJourneys($("#journeytrackers").val());
		});

		$( "#pdfdatefrom" ).datepicker({
			dateFormat: '@',
			onSelect: function(dateText, inst) {
			}
		});
		
		$( "#pdfdateto" ).datepicker({
			dateFormat: '@',
			onSelect: function(dateText, inst) {
			}
		});
		
		$( "#accordion" ).accordion({
			fillSpace: true,
			change: function(event, ui) {
				var active = $("#accordion").accordion("option","active");
				switch(active) {
					case 0:
						displayState = constants.DISPLAY_TRACKING;
						clearJourneys();
						reloadMarkers();
						mxnObj.removeAllPolylines();
						directionsDisplayObj.setMap(null);
						break;
					case 1:
						displayState = constants.DISPLAY_JOURNEY;
						$("#journeyloading").hide();
						mxnObj.removeAllPolylines();
						directionsDisplayObj.setMap(null);
						clearMarkers();
						break;
					case 2:
						displayState = constants.DISPLAY_DIRECTIONS;
						clearJourneys();
						clearMarkers();
						if (($('#intCalculateFromLat').val()=='') && ($('#intCalculateToLat').val()=='')) {
							cleanDialogCalculate();
							populateDestinationList();
							$("#dialog-calculate").dialog("open");							
						}	
						$('#intCalculateFromLat').val('');					
						$('#intCalculateToLat').val('');					
						break;
					case 3:
						displayState = constants.DISPLAY_STATIC;
						clearJourneys();
						clearMarkers();
						mxnObj.removeAllPolylines();
						directionsDisplayObj.setMap(null);
						break;
				}
				//clearMarkers();
				
				$("#journeytrackers").empty();
				$('#journeytrackers').append($("<option></option>").attr("value",0).text("Select Tracker..."));
				var vehiclelist = $("#vehiclelist").getDataIDs();
				$.each(vehiclelist, function(i, item) {
					$('#journeytrackers').append($("<option></option>").attr("value",item).text($("#vehiclelist").getCell(item,'name')));
				})
				if (selectedJourneyID != null) {
					$("#journeytrackers").val(selectedJourneyID);
					selectedJourneyID = null;
				}
				$("#journeytrackers").selectmenu();
			}
		});

		//all hover and click logic for buttons
		$(".fg-button:not(.ui-state-disabled)")
			.hover(
				function(){ 
					$(this).addClass("ui-state-hover"); 
				},
				function(){ 
					$(this).removeClass("ui-state-hover"); 
				}
			)
			.mousedown(function(){
				$(this).parents('.fg-buttonset-single:first').find(".fg-button.ui-state-active").removeClass("ui-state-active");
				if( $(this).is('.ui-state-active.fg-button-toggleable, .fg-buttonset-multi .ui-state-active') ){ $(this).removeClass("ui-state-active"); }
				else { $(this).addClass("ui-state-active"); }	
			})
			.mouseup(function(){
				if(! $(this).is('.fg-button-toggleable, .fg-buttonset-single .fg-button,  .fg-buttonset-multi .fg-button') ){
					$(this).removeClass("ui-state-active");
				}
			});
	});

</script>

</head>
<body>
<!-- manually attach allowOverflow method to pane -->
<div class="ui-layout-north" onmouseover="layoutObj.allowOverflow('north')" onmouseout="layoutObj.resetOverflow(this)" style="overflow: hidden; padding-top: 0px; padding-left: 0px; padding-bottom: 0px; padding-right: 0px;">
	<div style="position: relative;">
		<div class="fg-toolbar ui-widget-header ui-corner-all ui-helper-clearfix" style="height: 32px; overflow: hidden;">
			<div class="fg-buttonset ui-helper-clearfix" style="position:absolute; right:0; 1090px; width:160px; height: 32px;">			
				<a href="[var.brandingLogoURL]"><img src="[var.brandingLogoImg]" height="35" /></a>
			</div>
			<div class="fg-buttonset ui-helper-clearfix" style="position:absolute; left:10px; width:60px; height: 32px;">
				<a tabindex="0" href="#search-engines" class="fg-button fg-button-icon-right ui-widget ui-state-default ui-corner-all" id="flat" style="padding-top: 6px; padding-bottom: 9px;">
					<span class="ui-icon ui-icon-triangle-1-s"></span><span style="">Menu</span>
				</a>
				<div id="menu" class="hidden">
				<ul>
					<li><a href="#" action="connection">Login/Logout</a></li>
					<li><a href="#" action="reset">Reset&nbsp;Layout</a></li>
					<li><a href="#" action="settings">Settings</a></li>
					<li><a href="#" action="about">About</a></li>
				</ul>
				</div>
			</div>
			<div class="fg-buttonset ui-helper-clearfix" style="position:absolute; left:80px; width:30px; height: 32px;">
				<button id="btnHome" class="fg-toolbar-websocket-button"><img src="img/icons/open-house.gif"></button>
			</div>
			<div class="fg-buttonset ui-helper-clearfix" style="position:absolute; left:130px; top:14px; width:170px; height: 32px; padding-top: 0px;">
				<span id="connectionStatus" style="background-color:red; padding-top:7px; padding-bottom:7px; padding-left:5px; padding-right:5px; margin-top:0px; width:200px; text-align:center; border-radius: 5px;">
					Disconnected
				</span>
			</div>
			<div class="fg-buttonset ui-helper-clearfix" style="position:absolute; left:230px; width:130px; height: 32px;">
				<select id="optApi" name="optApi" class="ui-widget select">
					<option value="[blk2.value; block=option]">[blk2.option]</option>
					<option>[onshow.mapapi;ope=html;select]</option>
				</select>
			</div>
			<div class="fg-buttonset ui-helper-clearfix" style="position:absolute; left:370px; width:350px; height: 32px;">
    			Search: <input type="text" id="txtSearch" value="" name="txtSearch" size="30">
	    		<button id="btnSearch" class="fg-toolbar-websocket-button">Go</button>
			</div>
			<!---div class="fg-buttonset ui-helper-clearfix" style="position:absolute; left:770px; width:30px; height: 32px;">
				<button id="btnDebug" class="fg-toolbar-websocket-button"></button>
			</div--->
			<div class="fg-buttonset ui-helper-clearfix" style="position:absolute; left:970px; top:14px; width:110px; height: 32px;">
				<span id="iconSignal" class="ui-icon ui-icon-signal"></span>
			</div>
		</div>
	</div>
</div>

<!-- allowOverflow auto-attached by option: west__showOverflowOnHover = true -->
<div class="ui-layout-west">
	&nbsp;
</div>

<div class="ui-layout-south">
	<button class="south-toggler">Close Me</button>
</div>

<div class="ui-layout-east" id="layout-east">
	<div id="accordion">
		<h3><a href="#">Trackers</a></h3>
		<div style="padding:0px;" id="vehiclelistcontainer"; >
			<table id="vehiclelist"></table>
		   	<div id="vehiclepager"></div>	
		</div>
		<h3><a href="#">Journeys</a></h3>
		<div style="padding:0px;">
			<div style="position: relative; height: 240px;">
				<div style="position:absolute; left:20px; top:5px; ">
					<select id="journeytrackers" style="width:210px;"></select>
				</div>
				<div style="position:absolute; left:20px; top:50px; ">
					<div id="journeydate"></div>
				</div>
				<div style="position:absolute; left:250px; top:60px; height: 100px; ">
					<img id="journeyloading" src="img/ajax-loader.gif">
				</div>				
				<div id="journeynothing" style="position:absolute; left:250px; top:200px; height: 100px; font-style: italic ">
					Nothing Found
				</div>				
			</div>
			<table id="journeylist" style=""></table>
		</div>
		<h3><a href="#">Directions</a></h3>
		<div style="padding:0px;">
			<div id="directionsResultsDisplay" style="position: relative;">
			</div>
		</div>
		<h3><a href="#">Static Icons (Activities &amp; Jobs)</a></h3>
		<div style="padding:0px;" id="staticlistcontainer";>
			<table id="staticlist" style=""></table>
		</div>
	</div>
</div>

<div class="ui-layout-center" style="overflow: hidden;">
	<div id="microsoft" class="mapstraction" style="display:none; width:500px; height:500px; position:absolute; left:0px; top:0px;" ></div>
	<div id="yahoo" class="mapstraction" style="display:none; width:500px; height:500px; position:absolute; left:0px; top:0px;"></div>
	<div id="googlev3" class="mapstraction" style="display:none; width:500px; height:500px; position:absolute; left:0px; top:0px;"></div>
	<div id="openlayers" class="mapstraction" style="display:none; width:500px; height:500px; position:absolute; left:0px; top:0px;"></div>
	<div id="microsoft7" class="mapstraction" style="display:none; width:500px; height:500px; position:absolute; left:0px; top:0px;"></div>
</div>

<div id="dialog-logon" title="Login to Map" style="overflow: hidden;">
	<form>
	<div>
		<a href="[var.brandingLogoURL]"><img src="[var.brandingDialogImg]"></a>
    	[var.brandingLoginDialog;htmlconv=no]
	</div>
	<fieldset>
	   	<table>
        	<tr>
            	<td><label for="txtUsername">User</label></td>
                <td><input type="text" name="txtUsername" id="txtUsername" value="[var.txtUsename]" class="text ui-widget-content ui-corner-all" /></td>
            </tr>
        	<tr>
            	<td><label for="txtPassword">Password</label></td>
                <td><input type="password" name="txtPassword" id="txtPassword" value="[var.txtPassword]" class="text ui-widget-content ui-corner-all" /></td>
            </tr>
        	<tr>
            	<td><label for="bitSaveDetails">Save Details?</label></td>
                <td><input type="checkbox" name="bitSaveDetails" id="bitSaveDetails" value="wspass" class="text ui-widget-content ui-corner-all" [var.txtSaveCookie] /></td>
            </tr>
            <tr>
            	<td colspan="2"><span id="invalidmsg">&nbsp;</span></td>
            </tr>
        </table>
	</fieldset>
	</form>
</div>

<div id="dialog-confirm" title="Force Connect?"> 
	<p class="ui-state-error">
    	<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 40px 0;"></span>
    	<span style="padding:0px;">This account is logged on elsewhere. Would you like to disconnect it?</span>
	</p>
</div>

<div id="dialog-unsupported" title="Unsupported Browser">
	<p class="ui-state-error">
    	<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 40px 0px;"></span>
    	<span style="padding:0px;">You need a more up to date browser to view the map. Please use the a recent copy of Firefox, Google Chome or an iPad.</span>
     </p>
</div>

<div id="dialog-about" title="About">
	<p class="ui-state-info">
		<div>
			<img src="[var.brandingDialogImg]">
		</div>
		<div style="text-align: center; padding-top: 20px;" >
    	[var.brandingAboutDialog;htmlconv=no]
		</style>
     </p>
</div>

<div id="dialog-searchfailed" title="Location Search Error"> 
	<p class="ui-state-error">
    	<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 40px 0;"></span>
    	<span style="padding:0px;">Your search could not find this location</span>
	</p>
</div>

<div id="dialog-searchmultiple" title="Location Search Results">  
	<p class="ui-state-erinforor">
    	<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 40px 0;"></span>
    	<div style="margin:0px 0px 10px 0px;">Your search returned the following possible matches</div>
    	<span id="searchresults" class="dialogsearchresults"></span>
		<div style="margin:10px 0px 0px 0px;">
   			Search again: <input type="text" id="txtSearchInline" value="" name="txtSearchInline" size="40">
    		<button id="btnSearchInline">Go</button>
    		<input type="hidden" id="txtSearchSource" value="">
		</div>
	</p>
</div>

<div id="dialog-settings" title="Settings">
	<div id="tbsSettings" >
		<ul style="width: 360px;">
			<li><a href="#tabs-1">General</a></li>
			<li><a href="#tabs-2">Map</a></li>
			<li><a href="#tabs-3">Google</a></li>
			<li><a href="#tabs-4">Open Layers</a></li>
		</ul>
		<div id="tabs-1">
			<table>
				<tr>
					<td width="55%">Vehicle Grid</td>
					<td width="45%">
						<select id="optGridRowNormal" name="optGridRowNormal" class="ui-widget select" style="z-index: 4200; width: 180px;">
							<option value="grid-row-normal-yellow">Yellow</option>
							<option value="grid-row-normal-white">White</option>
							<option value="grid-row-normal-red">Red</option>
							<option value="grid-row-normal-green">Green</option>
							<option value="grid-row-normal-blue">Blue</option>
						</select>				
					</td>
				</tr>
				<tr>
					<td>Vehicle Updated</td>
					<td>
						<select id="optGridRowActive" name="optGridRowActive" class="ui-widget select" style="z-index: 4200; width: 180px;">
							<option value="grid-row-active-yellow">Yellow</option>
							<option value="grid-row-active-white">White</option>
							<option value="grid-row-active-red">Red</option>
							<option value="grid-row-active-green">Green</option>
							<option value="grid-row-active-blue">Blue</option>
						</select>				
					</td>
				</tr>
				<tr>
					<td>Update Duration</td>
					<td>
						<select id="optActivityHighlight" name="optActivityHighlight" class="ui-widget select" style="z-index: 4200; width: 180px;">
							<option value="0">Off</option>
							<option value="1000">1 Second</option>
							<option value="2000">2 Seconds</option>
							<option value="3000">3 Seconds</option>
						</select>				
					</td>
				</tr>
				<tr>
					<td>Inactive Vehicle</td>
					<td>
						<select id="optInactiveVehicle" name="optInactiveVehicle" class="ui-widget select" style="z-index: 4200; width: 180px;">
							<option value="0">Off</option>
							<option value="1">1 Hour</option>
							<option value="2">2 Hours</option>
							<option value="3">3 Hours</option>
							<option value="6">6 Hours</option>
							<option value="12">12 Hours</option>
							<option value="24">24 Hours</option>
						</select>				
					</td>
				</tr>
				<tr>
					<td>Display Inactive</td>
					<td>
						<select id="optShowInactive" name="optShowLabels" class="ui-widget select" style="z-index: 4200; width: 180px;">
							<option value="On">Yes</option>
							<option value="Off">No</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Distance and Speed</td>
					<td>
						<select id="optSpeedUnits" name="optSpeedUnits" class="ui-widget select" style="z-index: 4200; width: 180px;">
							<option value="mile">Miles / mph</option>
							<option value="km">Kilometers / kph</option>
							<option value="nautical">Nautical miles / knots</option>
						</select>				
					</td>
				<tr>
					<td>Theme</td>
					<td>
						<select id="optThemePicker" name="optThemePicker" class="ui-widget select" style="z-index: 4200; width: 180px;">
							<option value="[blk1.value; block=option]">[blk1.option]</option>
							<option>[onshow.themename;ope=html;select]</option>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<div id="tabs-2">
			<table>
				<tr>
					<td width="55%">Default Icon Type</td>
					<td width="45%">
						<select id="optMarkerType" name="optMarkerType" class="ui-widget select" style="z-index: 4200; width: 180px;">
							<option value="[blk3.value; block=option]">[blk3.option]</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Display Labels</td>
					<td>
						<select id="optShowLabels" name="optShowLabels" class="ui-widget select" style="z-index: 4200; width: 180px;">
							<option value="On">On</option>
							<option value="Off">Off</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Home Position</td>
					<td>
						<button id="btnSetHome">Set Here</button><span id="msgHomePositionSet"></span>
					</td>
				</tr>
				<tr>
					<td>Find Zoom Level</td>
					<td>
						<select id="optFindZoom" name="optFindZoom" class="ui-widget select" style="z-index: 4200; width: 180px;">
							<option value="10">10</option>
							<option value="11">11</option>
							<option value="12">12</option>
							<option value="13">13</option>
							<option value="14">14</option>
							<option value="15">15</option>
							<option value="16">16</option>
							<option value="17">17</option>
							<option value="18">18</option>
							<option value="19">19</option>
							<option value="20">20</option>
							<option value="21">21</option>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<div id="tabs-3">
			<table>
				<tr>
					<td>Display Traffic</td>
					<td>
						<select id="optShowTraffic" name="optShowTraffic" class="ui-widget select" style="z-index: 4200; width: 180px;">
							<option value="On">On</option>
							<option value="Off">Off</option>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<div id="tabs-4">
			<table>
				<tr>
					<td>Display Seamap</td>
					<td>
						<select id="optShowSeamap" name="optShowSeamap" class="ui-widget select" style="z-index: 4200; width: 180px;">
							<option value="On">On</option>
							<option value="Off">Off</option>
						</select>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

<div id="dialog-edit" title="Edit Unit">
	<p class="ui-state-info">
		<table>
			<tr>
				<td width="30%">Name</td>
				<td width="70%">
					<input type="hidden" name="intUnitID" id="intUnitID" value="" />	
					<input type="text" name="txtUnitName" id="txtUnitName" size="30" maxlength="30" value="" />	
				</td>
			</tr>
			<tr>
				<td width="30%">Icon</td>
				<td width="70%">
					<select id="optIconType" name="optIconType" class="ui-widget select" style="z-index: 4200; width: 200px;">
						<option value="[blk4.value; block=option]">[blk4.option]</option>
					</select>
				</td>
			</tr>
		</table>
     </p>
</div>

<div id="dialog-calculate" title="Calculate Journey">
	<p class="ui-state-info">
		<table>
			<tr>
				<td width="10%">From</td>
				<td width="70%">
					<input type="hidden" name="intUnitIDCalculate" id="intUnitIDCalculate" value="" />
					<input type="hidden" name="intStaticIDCalculate" id="intStaticIDCalculate" value="" />	
					<input type="hidden" name="" id="intCalculateFromLat" value="" />	
					<input type="hidden" name="" id="intCalculateFromLon" value="" />						
					<input type="text" name="txtCalculateFrom" id="txtCalculateFrom" size="50" maxlength="255" value="" />	
				</td>
				<td width="20%">
					<input type="button" onclick="doSearchSetJourneyFromFind()" value="...">
					<button type="submit" style="border: 0; background: transparent" onclick="doSearchSetJourneyFromItem()" >
					    <img src="img/pin.gif" alt="Marker" />
					</button>
				</td>
			</tr>
			<tr>
				<td width="10%">To</td>
				<td width="70%">
					<input type="text" name="txtCalculateTo" id="txtCalculateTo" size="50" maxlength="255" value="" />	
					<input type="hidden" name="" id="intCalculateToLat" value="" />	
					<input type="hidden" name="" id="intCalculateToLon" value="" />						
				</td>
				<td width="20%">
					<input type="button" onclick="doSearchSetJourneyToFind()" value="...">
					<button type="submit" style="border: 0; background: transparent" onclick="doSearchSetJourneyToItem()" >
					    <img src="img/pin.gif" alt="Marker" />
					</button>
				</td>
			</tr>
			<tr>
				<td width="10%">Via</td>
				<td width="70%">
					<input type="text" name="txtCalculateVia" id="txtCalculateVia" size="50" maxlength="255" value="" />	
					<input type="hidden" name="" id="intCalculateViaLat" value="" />	
					<input type="hidden" name="" id="intCalculateViaLon" value="" />						
				</td>
				<td width="20%">
					<input type="button" onclick="doSearchSetJourneyViaFind()" value="...">
					<button type="submit" style="border: 0; background: transparent" onclick="doSearchSetJourneyViaItem()" >
					    <img src="img/pin.gif" alt="Marker" />
					</button>
				</td>
			</tr>
		</table>
     </p>
</div>

<div id="dialog-location" title="Choose Location">
	<p class="ui-state-info">
		<table>
			<tr>
			<tr height="200px" valign="top">
				<td width="100%" align="center">
					<div style="height: 380px; width: 360px;  overflow-x: none; overflow-y: auto;">
						<table id="destinationlist" style=""></table>
					   	<div id="destinationpager"></div>
					   	<input type="hidden" id="destinationpick" value="">	
					   	<input type="hidden" id="destinationchosen" value="">	
					</div>
				</td>
			</tr>
		</table>
     </p>
</div>


<div id="dialog-pdfready" title="Journey PDF Report">
	<p class="ui-state-info">
		<table>
			<tr>
				<td>Your Journey PDF Report is now available.</td>
			</tr>
		</table>
     </p>
</div>


<div id="dialog-pdf" title="Generate PDF">
	<p class="ui-state-info">
		<table width="460">
			<tr>
				<td width="15%">
					Unit: 
				</td>
				<td width="85%">
					<span id="txtUnitNamePDF"></span>	
					<input type="hidden" name="intTrackerIDPDF" id="intTrackerID" value="" />	
					<input type="hidden" name="intUnitIDPDF" id="intUnitIDPDF" value="" />	
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
		<table width="460">
			<tr>
				<td colspan="4">
					<em>Select up to 7 consecutive days</em>
				</td>
			</tr>
			<tr>
				<td width="45%">
					From<br>
					<div id="pdfdatefrom"></div>
				</td>
				<td width="10%">
					&nbsp;
				</td>
				<td width="45%">
					To<br>
					<div id="pdfdateto"></div>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
		</table>
		<table width="460">
			<tr>
				<td width="15%">
					Unit: <br>
				</td>
				<td width="85%">
					<select name="txtDistanceUnitPDF" id="txtDistanceUnitPDF">
						<option value="miles">Miles</option>
						<option value="km">Kilometers</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					Display: <br>
				</td>
				<td>
					<input type="checkbox" name="boolDisplayPDF" id="boolDisplayPDF" value="1">
					<em>(you will be notified when ready)</em>
				</td>
			</tr>
			<tr>
				<td>
					Email: <br>
				</td>
				<td>
					<input type="text" name="txtEmailPDF" id="txtEmailPDF" value="" size="50"><br>
					<em>(optional)</em>
				</td>
			</tr>
		</table>
     </p>
</div>

<div class="contextMenu" id="vehicleContext" style="display:none">
    <ul style="width: 180px !important">
        <li id="showvehicle">
            <span style="font-size:12px; font-family:Verdana">Show Vehicle</span>
        </li>
        <li id="showjourney">
            <span style="font-size:12px; font-family:Verdana">Show Journeys (Snail Trail)</span>
        </li>
        <li id="showjourneyreport">
            <span style="font-size:12px; font-family:Verdana">Journey Reports</span>
        </li>
        <li id="showeditunit">
            <span style="font-size:12px; font-family:Verdana">Edit Name/Icon</span>
        </li>
        <li id="showcalculatejourney">
            <span style="font-size:12px; font-family:Verdana">Calculate Journey</span>
        </li>
    </ul>
</div>

<div class="staticMenu" id="staticContext" style="display:none">
    <ul style="width: 180px !important">
        <li id="showstatic">
            <span style="font-size:12px; font-family:Verdana">Show Icon</span>
        </li>
        <li id="removestatic">
            <span style="font-size:12px; font-family:Verdana">Remove Icon</span>
        </li>
        <li id="showstaticjourney">
            <span style="font-size:12px; font-family:Verdana">Calculate Journey</span>
        </li>
    </ul>
</div>

</body>
</html>