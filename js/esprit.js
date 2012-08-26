var esprit = {

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
        $.post('/js-error-report', data);
    }

};
