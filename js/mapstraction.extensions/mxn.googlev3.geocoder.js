mxn.register('googlev3', {      

Geocoder: {
        
        init: function() {
                this.geocoders[this.api] = new google.maps.Geocoder();
        },
        
        geocode: function(address){
                var me = this;
                
                if (!address.hasOwnProperty('address') || address.address === null || address.address === '') {
                        address.address = [ address.street, address.locality, address.region, address.country ].join(', ');
                }
                
                if (address.hasOwnProperty('lat') && address.hasOwnProperty('lon')) {
                        var latlon = address.toProprietary(this.api);
                        this.geocoders[this.api].geocode({'latLng': latlon }, function(results, status) {
                                me.geocode_callback(results, status);
                        });
                } else {
                        this.geocoders[this.api].geocode({'address': address.address }, function(results, status) {
                                me.geocode_callback(results, status);
                        });
                }
        },
        
        geocode_callback: function(results, status){
                var return_location = [];

                if (status != google.maps.GeocoderStatus.OK) {
                        this.error_callback(status);
                } 
                else {
                        for (var ridx=0; ridx<results.length; ridx++) {
                                var location = {};
                                location.street = '';
                                location.locality = '';
                                location.postcode = '';
                                location.region = '';
                                location.country = '';
                                location.formatted_address = '';

                                var place = results[ridx];
                                var streetparts = [];

                                for (var i = 0; i < place.address_components.length; i++) {
                                        var addressComponent = place.address_components[i];
                                        for (var j = 0; j < addressComponent.types.length; j++) {
                                                var componentType = addressComponent.types[j];
                                                switch (componentType) {
                                                        case 'country':
                                                                location.country = addressComponent.long_name;
                                                                break;
                                                        case 'administrative_area_level_1':
                                                                location.region = addressComponent.long_name;
                                                                break;
                                                        case 'locality':
                                                                location.locality = addressComponent.long_name;
                                                                break;
                                                        case 'street_address':
                                                                location.street = addressComponent.long_name;
                                                                break;
                                                        case 'postal_code':
                                                                location.postcode = addressComponent.long_name;
                                                                break;
                                                        case 'street_number':
                                                                streetparts.unshift(addressComponent.long_name);
                                                                break;
                                                        case 'route':
                                                                streetparts.push(addressComponent.long_name);
                                                                break;
                                                }
                                        }
                                }
                        
                                if (location.street === '' && streetparts.length > 0) {
                                        location.street = streetparts.join(' ');
                                }       
                                
                                location.formatted_address = place.formatted_address;   
                        
                                location.point = new mxn.LatLonPoint(place.geometry.location.lat(), place.geometry.location.lng());
                        
                                return_location.push(location);
                        }
                        this.callback(return_location);
                }
        }
}
});