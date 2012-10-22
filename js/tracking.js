/*
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
*/
// JavaScript Document
// Webservice handling
var NetworkConnection;
var TrackingService;
			
// Parameter
function constants() {
}

constants.LOGON_TRUE		=	1;		// null
constants.LOGON_FALSE		=	2;		// string containing reply message
constants.WEBSOCKET_ERROR	=	3;		// string containing error 
constants.WEBSOCKET_CLOSED	=	4;		// string containing error
constants.ACCOUNTS			=	5;		// object containing all accounts
constants.ACCOUNT			=	6;		// object containing single account
constants.TRACKER			=	7;		// object containing tracker update
constants.PLACEHOLDER		=	8;		// object containing single account initialized from tracker call pre full account info
constants.UNSUPPORTED		= 	9;		// browser is unsupported
constants.LOGON_INVALID		= 	10;		// invalid username/password
constants.CONNECTED			= 	11;		// connected message
constants.DISCONNECTED		= 	12;		// disconnected message
constants.KEYVAL			=	13;		// keyvalue pairs
constants.JOURNEYLIST		=	14;		// keyvalue pairs
constants.REPORTCALLBACK	=	15;		// pdf callback
constants.ADDICON			=	16;		// add icon callback
constants.DELICON			=	17;		// delete icon callback


// general system constants collected here for good practice
constants.DEFAULT_GRIDROWNORMAL = 'grid-row-normal-yellow';	// grid background, normal
constants.DEFAULT_GRIDROWACTIVE = 'grid-row-active-yellow'; // grid background, highlighted
constants.DEFAULT_ACTIVITYHIGHLIGHT	= 1000;	// lenght of time a vehicle is highlighted when tracker data arrives
constants.DEFAULT_INACTIVEVEHICLE = 24;	// hours before timing a vehicle out as inactive 
constants.DEFAULT_LATITUDE = 53.357108745695996; // default map latitude...
constants.DEFAULT_LONGITUDE = -2.0874023437499933; // ... longitude
constants.DEFAULT_MAPZOOM = 6; // and zoom
constants.DEFAULT_FINDZOOM = 15; // zoom lefel on find
constants.DEFAULT_UPDATETICK = 15000; // milliseconds to apply grid changes
constants.DEFAULT_INITIALTICK = 2000; // and an initial tick to clear down
constants.DEFAULT_SHOWLABELS = "On"; // show map vehicles labels
constants.DEFAULT_MARKER = "Car_Blue"; // markers
constants.DEFAULT_SPEEDUNIT = "miles"; // markers
constants.DEFAULT_SHOWTRAFFIC = "Off";	// google traffic layer
constants.DEFAULT_SHOWSEAMAP = "Off";	// openlayers seamap layers
constants.DEFAULT_SHOWINACTIVE = "On";	// show inactive vehicles - by default on

// marker constants
constants.MARKERS =  ['Car_Blue',
'Car_Black',
'Car_Green',
'Car_Orange',
'Car_Pink',
'Car_Red',
'Car_Yellow',
'Truck_Blue',
'Truck_Black',
'Truck_Green',
'Truck_Orange',
'Truck_Pink',
'Truck_Red',
'Truck_Yellow',
'Pda_Blue',
'Pda_Black',
'Pda_Green',
'Pda_Orange',
'Pda_Pink',
'Pda_Red',
'Pda_Yellow',
'LargeVan_Blue',
'LargeVan_Black',
'LargeVan_Green',
'LargeVan_Orange',
'LargeVan_Pink',
'LargeVan_Red',
'LargeVan_Yellow',
'SmallVan_Blue',
'SmallVan_Black',
'SmallVan_Green',
'SmallVan_Orange',
'SmallVan_Pink',
'SmallVan_Red',
'SmallVan_Yellow',
'Boat_Blue',
'Boat_Black',
'Boat_Green',
'Boat_Orange',
'Boat_Pink',
'Boat_Red',
'Boat_Yellow',
'Pickup_Blue',
'Pickup_Black',
'Pickup_Green',
'Pickup_Orange',
'Pickup_Pink',
'Pickup_Red',
'Pickup_Yellow',
'Flatbed_Blue',
'Flatbed_Black',
'Flatbed_Green',
'Flatbed_Orange',
'Flatbed_Pink',
'Flatbed_Red',
'Flatbed_Yellow',
'FlatbedCrane_Blue',
'FlatbedCrane_Black',
'FlatbedCrane_Green',
'FlatbedCrane_Orange',
'FlatbedCrane_Pink',
'FlatbedCrane_Red',
'FlatbedCrane_Yellow',
'FlatbedCargo_Blue',
'FlatbedCargo_Black',
'FlatbedCargo_Green',
'FlatbedCargo_Orange',
'FlatbedCargo_Pink',
'FlatbedCargo_Red',
'FlatbedCargo_Yellow'];
						
// direction image constants
constants.DIRECTIONS = [
						"<img src='img/arrows/north.png' title='North' />",
						"<img src='img/arrows/northeast.png' title='North East' />",
						"<img src='img/arrows/east.png' title='East' />",
						"<img src='img/arrows/southeast.png' title='South East' />",
						"<img src='img/arrows/south.png' title='South' />",
						"<img src='img/arrows/southwest.png' title='South West' />",
						"<img src='img/arrows/west.png' title='West' />",
						"<img src='img/arrows/northwest.png' title='North West' />"
						];					
						
constants.TABLE_ID			=	0;
constants.TABLE_CONNECTED	=	1;
constants.TABLE_IGON		=	2;
constants.TABLE_NAME		=	3;
constants.TABLE_UPDATED		=	4;
constants.TABLE_SPEED		=	5;
constants.TABLE_DIRECTION	=	6;
constants.TABLE_EDIT		=	7;
constants.TABLE_LATITUDE	=	8;
constants.TABLE_LONGITUDE	=	9;
constants.TABLE_ALTITUDE	=	10;
constants.TABLE_OKOKM		=	11;
constants.TABLE_ICON		=	12;
constants.TABLE_TIMESTAMP	=	13;
constants.TABLE_KPH			= 	14;
constants.TABLE_SCROLL		=	15;

constants.DISPLAY_TRACKING	 = 0;
constants.DISPLAY_JOURNEY	 = 1;
constants.DISPLAY_TRAFFIC	 = 2;
constants.DISPLAY_DIRECTIONS = 4;
constants.DISPLAY_STATIC	 = 5;

constants.STATIC_LATITUDE	= 3;
constants.STATIC_LONGITUDE	= 4;

constants.GEOCODE_DELAY	= 3000;
constants.GEOCODE_RETRY	= 6000;

//---------------------------------------------------------------------------------------------//
// Network connection
//---------------------------------------------------------------------------------------------//
NetworkConnection = {
	socket: null, 

	init: function (openCallback, dataCallback) {

	 	if ('WebSocket' in window)  //chrome
		{
			NetworkConnection.socket = new WebSocket(TrackingService.url);
		}
		else if ('MozWebSocket' in window) //Firefox
		{
			NetworkConnection.socket = new MozWebSocket(TrackingService.url);		
		}
		else { // incompatible
			var data = new Object();
			data.Unsupported = true;
			dataCallback(data);
			return;
		};
		
		NetworkConnection.socket.onopen = openCallback;
		
		NetworkConnection.socket.onmessage = function (event) {
			//console.log(event);		
			try {
				var data = JSON.parse(event.data.replace(/[\s\x00]/g, ' '));
				dataCallback(data);
			}
			catch(err) {
				console.log("NetworkConnection.socket.onmessage");
				console.log(err);
				console.log(event);
			}
		};
		
		NetworkConnection.socket.onerror = function (event) {
			//console.log("WebSocket error: " + event.data);
			var data = new Object();
			data.Error = true;
			data.ErrorMsg = event.data;
			dataCallback(data);
		};
		
		NetworkConnection.socket.onclose = function () {
			//console.log("WebSocket closed");
			var data = new Object();
			data.Error = true;
			data.ErrorMsg = "";
			dataCallback(data);
		};
	},

	send: function (data) {
		NetworkConnection.socket.send(JSON.stringify(data));
	},

	close: function () {
		//console.log("close socket");
		NetworkConnection.socket.close();
	}
};

//---------------------------------------------------------------------------------------------//
// Account
//---------------------------------------------------------------------------------------------//
function Account(desc) {
	this.id = desc.id;
	this.name = desc.name;
	this.phone = desc.telNumber;
	this.icon = desc.icon;
	this.connected = desc.connected;
	this.mapObject = null;
	this.latitude = null;
	this.longitude = null;
};

Account.prototype.update = function (desc) {
	//console.log("Account update");
	this.name = desc.name;
	this.phone = desc.telNumber;
	this.icon = desc.icon;
};

Account.prototype.updateTracker = function (trackerDesc) {
	//console.log("Account updateTracker");
	//console.log(trackerDesc);
	this.latitude = trackerDesc.gps.latitude;
	this.longitude = trackerDesc.gps.longitude;
};

//---------------------------------------------------------------------------------------------//
// StaticItem - not use StaticItem so not to confuse with static reserve word
//---------------------------------------------------------------------------------------------//
function StaticItem(desc) {
	this.id = desc.id;
	this.text = desc.text;
	this.icon = desc.icon;
	this.mapObject = null;
	this.latitude = desc.lat;
	this.longitude = desc.lon;
};

StaticItem.prototype.update = function (desc) {
	//console.log("Account update");
	this.text = desc.text;
	this.icon = desc.icon;
};


//---------------------------------------------------------------------------------------------//
// Tracking service 
//---------------------------------------------------------------------------------------------//
TrackingService = {
	username: '',
	password: '',
	sessionKick: false,
	url: '',
	accounts: new Object,
	staticitems: new Object,
	applicationCallback: null,

	init: function (username, password, sessionKick, url, callback) {
		TrackingService.username = username;
		TrackingService.password = password;
		TrackingService.sessionKick = sessionKick;
		TrackingService.url = url;
		TrackingService.applicationCallback = callback;
		NetworkConnection.init(TrackingService.sendCredentials, TrackingService.dispatchMessage);
	},

	sendCredentials: function () {
		NetworkConnection.send({
			Login: {
				username: TrackingService.username,
				password: TrackingService.password,
				sessionID: Math.floor(Math.random()*0xffffffff),
				sessionKick: TrackingService.sessionKick
			}
		});
	},
	
	sendAccount: function (id,name,icon) {
		//console.log('SEND ACCOUNT');
		//console.log(id);
		//console.log(name);
		//console.log(icon);
		NetworkConnection.send({
			setAccount: {
				id: id,
				name: name,
				icon: icon
			}
		});
	},
		
	sendKeyValuePairs: function (gridrownormal,gridrowactive,activityhighlight,inactivevehicle,theme,lat,lon,magnify,showlabels,markers,speedunit,traffic,seamap,findzoom,showinactive) {
		// note that variables sent must be strings
		NetworkConnection.send({
			keyval: {
				showlabels: showlabels,
				gridrownormal: gridrownormal,
				gridrowactive: gridrowactive,
				activityhighlight: activityhighlight,
				inactivevehicle: inactivevehicle,
				theme: theme,
				lat: lat,
				lon: lon,
				magnify: magnify,
				markers: markers,
				speedunit: speedunit,
				traffic: traffic,
				seamap: seamap,
				findzoom: findzoom,
				showinactive: showinactive
			}
		});
	},

	sendKeyValuePdfEmail: function (pdfemail) {
		// note that variables sent must be strings
		NetworkConnection.send({
			keyval: {
				pdfemail: pdfemail
			}
		});
	},

	
	sendJourneyRequest: function (trackerID,dateFrom,dateTo) {
		// send a request to return journeys
		NetworkConnection.send({
			journeyReq: {
				outputType: "data",
				trackerID: trackerID,
				dateFrom: dateFrom,
				dateTo: dateTo,
				includeFlags: 0, 
				inJnyLimitIntvalKm: -1,
				revGeoKmInterval: 0, 
			}
		});
	},

	sendPDFRequest: function (trackerID,dateFrom,dateTo,unit,email) {
		// send a request to return journeys	
		var localURL = document.createElement("a");
                localURL.href = document.URL;  // Get local URL
                $.get("dnsLookup.php?"+localURL.hostname, function(thisIp) {  //Get IP address of the domain in use
		NetworkConnection.send({
			journeyReq: {
				outputType: "pdf",
				trackerID: trackerID,
				dateFrom: dateFrom,
				dateTo: dateTo,
				includeFlags: 0, 
				inJnyLimitIntvalKm: 50,
				revGeoKmInterval: 0.5, 
				unit: unit,
				email: email,
				hosturl: thisIp,
			}
		});
		});
	},
	
	sendRemoveStatic: function (id) {
		// note that variables sent must be strings
		NetworkConnection.send({
			delIcon: [id]
		});
	},


	onLoginReply: function (reply) {
		//console.log(reply);
		if( reply.loggedIn ) {
			// TODO: make use of minActivitySecs, clientID
			TrackingService.applicationCallback(constants.LOGON_TRUE,null);
		}
		else {
			if (typeof(reply.dupeLogin) != 'undefined') {
				TrackingService.applicationCallback(constants.LOGON_FALSE,reply.message);
			}
			else {
				TrackingService.applicationCallback(constants.LOGON_INVALID,reply.message);
			}
		}
	},

	closeConnection: function () {
		NetworkConnection.send({
			Logout: {
			}
		});
		NetworkConnection.close();
	},

	// Adds or updates account.  Desc contains data, example
	// "id": 11104000068848, "name": "Freds Tracker", "telNumber": "07123 456784", "icon": 0 
	addOrUpdateAccount: function (desc) {
		var account = TrackingService.accounts[desc.id]; // grab id returned and find if we have a corresponding account by looking in accounts, if not create
		if( typeof(account) != 'undefined' )
			account.update(desc);
		else {
			account = new Account(desc);
			TrackingService.accounts[desc.id] = account;
		}
	},

	// Account data hasn't arrived yet, but create an account from what we have
	addAccountPlaceHolder: function(tracker) {
		desc = new Object;
		desc.id = tracker.id;
		desc.name = null;
		desc.phone = null;
		desc.icon = null;
		desc.connected = false;
		desc.mapObject = null;
		TrackingService.addOrUpdateAccount(desc);
		
		// callback to page to tell it we have a new acount
		TrackingService.applicationCallback(constants.PLACEHOLDER,desc);

		// now add our tracker data to the account we just created
		var target = TrackingService.accounts[tracker.id];
		if( typeof(target) != 'undefined' ) {
			target.updateTracker(tracker);
			TrackingService.applicationCallback(constants.TRACKER,tracker);
		}
		else {
			// there's some error	
		}
	},

	// Adds or updates static.  Desc contains data, example
	addOrUpdateStaticItems: function (data) {
		$.each(data, function(i, item) {
			var staticitem = TrackingService.staticitems[item.id]; 
			if( typeof(staticitem) != 'undefined' )
				staticitem.update(item);
			else {
				staticitem = new StaticItem(item);
				TrackingService.staticitems[item.id] = staticitem;
			}
		});
	},

	// dispatch method returned by webservice
	dispatchMessage: function (data) {
		
		if( typeof(data.loginReply) != 'undefined' ) {
			//console.log("LoginReply");
			TrackingService.onLoginReply(data.loginReply);
		}
		// multiple accounts returned
		else if( typeof(data.Accounts) != 'undefined' ) {
			//console.log("Accounts");
			//console.log(data.Accounts);
			$.each(data.Accounts, function () {
				TrackingService.addOrUpdateAccount(this)
			});
			TrackingService.applicationCallback(constants.ACCOUNTS,TrackingService.accounts);
		}
		// single account returned
		else if( typeof(data.Account) != 'undefined' ) {
			//console.log("Account");
			TrackingService.addOrUpdateAccount(data.Account);
			TrackingService.applicationCallback(constants.ACCOUNT,data.Account);
		}
		else if( typeof(data.Tracker) != 'undefined' ) {
			//console.log("Tracker");
			var target = TrackingService.accounts[data.Tracker.id];
			if( typeof(target) != 'undefined' ) {
				//console.log("Tracker");
				//console.log(data.Tracker);
				target.updateTracker(data.Tracker);
				TrackingService.applicationCallback(constants.TRACKER,data.Tracker);
			}
			else {
				// create account anyway because the data will probably be along later
				TrackingService.addAccountPlaceHolder(data.Tracker);
			}
		}
		else if( typeof(data.Error) != 'undefined' ) {
			//console.log("Error");
			if (data.ErrorMsg == "") {
				TrackingService.applicationCallback(constants.WEBSOCKET_CLOSED,data.ErrorMsg);
			}
			else {
				TrackingService.applicationCallback(constants.WEBSOCKET_ERROR,data.ErrorMsg);
			}
		}
		else if( typeof(data.Unsupported) != 'undefined' ) {
			//console.log("Unsupported");
			TrackingService.applicationCallback(constants.UNSUPPORTED,data.Account);
		}		
		else if( typeof(data.connected) != 'undefined' ) {
			//console.log("Connected");
			//console.log(data.connected);
			TrackingService.applicationCallback(constants.CONNECTED,data);
		}		
		else if( typeof(data.disconnected) != 'undefined' ) {
			//console.log("Disconnected");
			//console.log(data.disconnected);
			TrackingService.applicationCallback(constants.DISCONNECTED,data);
		}		
		else if( typeof(data.keyval) != 'undefined' ) {
			//console.log("Keyval");
			TrackingService.applicationCallback(constants.KEYVAL,data);
		}		
		else if( typeof(data.journeys) != 'undefined' ) {
			//console.log("Journeys");
			TrackingService.applicationCallback(constants.JOURNEYLIST,data);
		}		
		else if( typeof(data.reportCallback) != 'undefined' ) {
			TrackingService.applicationCallback(constants.REPORTCALLBACK,data);
		}		
		else if( typeof(data.addIcon) != 'undefined' ) {
			TrackingService.addOrUpdateStaticItems(data.addIcon);
			TrackingService.applicationCallback(constants.ADDICON,data);
		}				
		else if( typeof(data.delIcon) != 'undefined' ) {
			TrackingService.applicationCallback(constants.DELICON,data);
		}				
		else {
			console.log("Unknown message type");
			console.log(data);
		}
		//console.log(data);
	},
};
