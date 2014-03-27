<?php
/**
 * Handlebars Laravel extension.
 *
 * Class to allow helpers to be added to be used by
 * the accompanying engine
 *
 * @author    Mike Farrow <contact@mikefarrow.co.uk>
 * @license   Proprietary/Closed Source
 * @copyright Mike Farrow
 */

namespace Weyforth\Handlebars;

use Closure;

class Handlebars
{

    /**
     * Helper function store.
     *
     * @var array
     */
    protected $helpers = array();

    /**
     * Data function store.
     *
     * @var Closure
     */
    protected $data = null;


    /**
     * Add a helper to the render engine.
     *
     * @param string $name     Name of the helper to add.
     * @param string $function Helper function.
     *
     * @return void
     */
    public function addHelper($name, $function)
    {
        $this->helpers[$name] = $function;
    }


    /**
     * Add a data resolver to the render engine.
     *
     * @param Closure $function Data function.
     *
     * @return void
     */
    public function addDataResolver(Closure $function)
    {
        $this->data = $function;
    }


    /**
     * Retrieve all helpers.
     *
     * @return array
     */
    public function getHelpers()
    {
        return $this->helpers;
    }


    /**
     * Retrieve data to include in the template.
     *
     * @return array
     */
    public function getData()
    {
        $dataClosure = $this->data;
        return $dataClosure !== null ? $dataClosure() : array();
    }


}
