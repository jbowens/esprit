<?php

namespace esprit\core;

/**
 * This class implements a ViewResolver that resolves all responses 
 * to the same view. This resolver is useful as a final catchall
 * in a chain of resolvers. Because Views may execute arbitrary
 * logic, mapping all responses to a single View does not limit
 * possible functionality. By default, the \esprit\core\views\DefaultView
 * class is used. This class prints templates based on the path.
 *
 * @author jbowens
 */
class CatchallViewResolver implements ViewResolver {

    protected $view;

    public function __construct(View $view) {
        $this->view = $view;
    }

    /**
     * @see ViewResolver.resolve(Response $response)
     */
    public function resolve(Response $response) {
        return $view;
    }

}
