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
    recordError: function(error)
    {
        data = {
            'eName': error.name,
            'eMsg': error.message,
            'url': window.location.toString(),
            'host': window.location.host,
            'path': window.location.pathname,
            'user-agent': window.navigator.userAgent,
            'appCodeName': window.navigator.appCodeName
        };

        if( error.stack )
        {
            data.eStack = error.stack;
        }
        
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

        $.post('/js-error-record', data);
    },

    /**
     * Records an action event. The data surrounding the action will
     * be forwarded to the server through an ajax call. This is useful
     * for tracking user involvement with page elements on the page for
     * analytics.
     *
     * @param an esprit.Action object
     */
    recordAction: function(action)
    {
        var data = {};
        $.post('/action-record', data);
    }

};
