/*
 * jQuery Geolocation with a fallback for older browsers
 *
 * https://github.com/there4/jquery-geolocation
 *
 * Copyright (c) 2012 Craig Davis
 * Licensed under the MIT license.
 */

(function($, undefined){

    $.extend($.support, {
        geolocation: function(){
            return $.geolocation.support();
        }
    });

    $.geolocation = {
        options: {
            highAccuracy: false,
            track: false
        },
        
        find: function(success, error, options) {
            var method;
            
            success = success || function(){};
            error = error || function(){};
            options = options || {};
                
            if ($.geolocation.support()) {
                options = $.extend(
                    $.geolocation.options,
                    options
                );
                method = (options.track ? 'watchPosition' : 'getCurrentPosition');
                ($.geolocation.object())[method](
                    function(location){ success(location.coords); },
                    function(status) { 
                        // call our fallbackPosition
                        error(status.message);
                    },
                    { enableHighAccuracy: options.highAccuracy }
                );
            }
            else {
                // We use our proxy and InfoDB to lookup the location
                error();
            }
        },
        
        fallbackPosition: function(callback) {
            // call our proxy to fetch info from InfoDB
        },

        object: function() {
            return navigator.geolocation;
        },

        support: function() {
            return ($.geolocation.object()) ? true : false;
        }
    };

}(jQuery));
