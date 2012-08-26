/**
 * Defines the esprit object/namespace.
 *
 * @author jbowens
 * @since 2012-08-26
 */
var esprit = {

    /**
     * An array of functions that should be used to add data to any error reports
     * sent to the server.
     */
    errorDataExtractors: [],

    /**
     * Registers a function to be called whenever an error is reported. The function
     * that is registered should return an object of additional data it wants to be
     * sent with the error report. The properties of the object will be merged with
     * the default properties.
     *
     * @param extractor  the extractor function
     */
    registerErrorDataExtractor: function(extractor) {
        this.errorDataExtractors.push(extractor);
    },

    /**
     * Logs an error message through an ajax call to the server. Using this
     * everywhere can be tremendously useful for tracking down javascript
     * bugs.
     *
     * @param error  an Error object
     */
    errorReport: function(error)
    {
        data = {
            'error': error,
            'url': window.location.toString(),
            'host': window.location.host,
            'path': window.location.pathname,
            'user-agent': window.navigator.userAgent,
            'appCodeName': window.navigator.appCodeName
        };
        
        // If any error data extractors were registered, we should send along that
        // data with the error report as well.
        for( var dataExtractor in this.errorDataExtractors )
        {
            try {
                var additionalData = dataExtractor.extract( error );
                for( var attrname in additionalData )
                {
                    data[attrname] = additionalData[attrname];
                }
            } catch( err )
            {
                // Nothing we can do about this without causing an infinite loop.
            }
        }

        $.post('/js-error-report', data);
    }

};
