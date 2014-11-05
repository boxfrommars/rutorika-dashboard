<?php

namespace Rutorika\Dashboard\HTML;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class FormBuilderFacade extends IlluminateFacade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'formbuilder'; }

}