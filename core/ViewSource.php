<?php

namespace esprit\core;

/**
 * An interface for a source of View objects.
 * 
 * @author jbowens
 */
interface ViewSource {

    /**
     * Returns true iff this source is capable of instantiating
     * the given view.
     *
     * @param String $viewname  the view to check
     * @return true iff the view is defined within this source
     */
    public function isViewDefined( $viewName ); 

    /**
     * Instantiates the given view.
     *
     * @param String $viewname  the view to instantiate
     * @return View  the given view instance
     */
    public function instantiateView( $viewName );

}
