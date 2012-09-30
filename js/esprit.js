/**
 * Defines the esprit object/namespace.
 *
 * @author jbowens
 * @since 2012-08-26
 */
var esprit = {

    /**
     * A map from thranslation key to translation string.
     */
    translations: {},

    /**
     * Translates the given translation identifier into the correct string.
     * 
     * @param translationIdentifier  the translation identifier to translate
     * @return the identifier translated into the currently selected language
     */
    t: function(translationIdentifier) {
        try {
            if( ! this.translations[translationIdentifier] )
            {
                throw new Error("Could not find a translation for key: " + translationIdentifier);
                return '';
            }
            
            return this.translations[translationIdentifier];

        } catch(err) {
            this.recordError( err );
        }
    },

    /**
     * Adds a translation into the javascript base.
     */
    addTranslation: function( identifier, translation ) {
        try {
            this.translations[identifier] = translation;
        } catch(err) {
            this.recordError(err);
        }
    },

    /**
     * esprit.oop namespace contains useful methods for dealing
     * with object-oriented features like object inheritance.
     */
    oop: {
         
        /**
         * Creates a new object that extends the given super object.
         *
         * @param superObj  the object to extend
         * @param objData  the new object data to add
         */
        extend: function(superObj, objData)
        {
            
            var newObj;
            function F() {}
            F.prototype = superObj;
            newObj = new F();
            newObj.uber = superObj;

            for( var i in objData )
            {
                newObj[i] = objData[i];
            }

            return newObj;
        },

        /**
         * Mixes in the given trait object into the given target
         * object. 
         *
         * @param trait  an object with functionality that should be
         *               added to the given obj
         * @param objToModify  the object that should take on the trait's
         *                     functionality
         */
        mixIn: function(trait, objToModify)
        {
            for( var key in trait )
            {
                if( ! objToModify.hasOwnProperty(key) )
                    objToModify[key] = trait[key];
            }

            return objToModify;
        }

    },

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
     * Defines an Action object used for recording user 
     * behavior.
     */
    Action: {

        /**
         * Creates a new esprit.Action object with the given
         * identifier.
         *
         * @param identifier  the identifier of the action event
         */
        construct: function(identifier) {
            return esprit.oop.extend(esprit.Action, { 'identifier': identifier });
        },

        identifier: null,

        getIdentifier: function() {
            return this.identifier;
        },

        toString: function() {
            return "esprit.Action("+this.identifier+")";
        }

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
        try {
            var data = {
                identifier: action.getIdentifier()
            };
            $.post('/action-record', data);
        } catch(err)
        {
            esprit.recordError(err);
        }
    }

};

// Load any translations stored in the window object
if( window.espritTranslations )
{
    try {
        for( var key in window.espritTranslations )
        {
            esprit.addTranslation(key, window.espritTranslations[key]);
        }
    }
    catch(err) 
    { 
        esprit.recordErr(err);
    }
}
