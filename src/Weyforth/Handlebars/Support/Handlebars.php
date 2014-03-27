<?php
/**
 * Facade to expose Handlebars class.
 *
 * @author    Mike Farrow <contact@mikefarrow.co.uk>
 * @license   Proprietary/Closed Source
 * @copyright Mike Farrow
 */

namespace Weyforth\Handlebars\Support;

use Illuminate\Support\Facades\Facade;

class Handlebars extends Facade
{


    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'handlebars';
    }


}
