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
     * Data function store.
     *
     * @var Closure
     */
    protected $vars = array();


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


    /**
     * Add variables to the variable store.
     *
     * @param array $vars Key-value pairs to add.
     *
     * @return void
     */
    public function addVars(array $vars)
    {
        if (array_key_exists('__env', $vars)) {
            unset($vars['__env']);
        }

        if (array_key_exists('app', $vars)) {
            unset($vars['app']);
        }

        foreach ($vars as $key => $var) {
            if (is_array($var) && array_key_exists($key, $this->vars)) {
                $this->vars[$key] = array_merge($this->vars[$key], $var);
            } else {
                $this->vars[$key] = $var;
            }
        }
    }


    /**
     * Retrieve data to include in the template.
     *
     * @return array
     */
    public function outputVars()
    {
        $vars = $this->vars;

        $rendered = "<script>\n";

        $rendered .= "if (typeof handlebars == 'undefined') var handlebars = { templates: {} };\n";

        $jsonOptions = (JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

        foreach ($vars as $key => $value) {
            $rendered .= 'handlebars.'.$key.' = '.json_encode(
                $value,
                $jsonOptions
            ).";\n";
        }

        $rendered .= "</script>\n";

        return $rendered;
    }


}
