<?php
/**
 * Handlebars extension to the Laravel View class.
 *
 * Loads and renders handlebars templates, and stores
 * the templates and variable in memory for inclusion
 * in source files later on.
 *
 * @author    Mike Farrow <contact@mikefarrow.co.uk>
 * @license   Proprietary/Closed Source
 * @copyright Mike Farrow
 */

namespace Weyforth\Handlebars;

use Illuminate\View\Engines\EngineInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Weyforth\Handlebars\Support\Handlebars as HandlebarsHelpers;
use LightnCandy;

class HandlebarsEngine implements EngineInterface
{


    /**
     * Constructor.
     *
     * @param Filesystem $files Files instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }


    /**
     * Get the nested template.
     *
     * Renders template using data supplied, as well as inserting commonly
     * required data such as errors, messages and input. Also stores
     * unrendered template and data in memory for inclusion later on.
     *
     * @param string $path Path of template.
     * @param array  $data Data to inject into template.
     *
     * @return string Rendered template
     */
    public function get($path, array $data = array())
    {
        $view = $this->files->get($path);
        $app  = app();

        $helpers = HandlebarsHelpers::getHelpers();
        $args    = array(
            'helpers' => array()
        );

        foreach ($helpers as $name => $function) {
            $args['helpers'][$name] = $function;
        }

        $paths = Config::get('view.paths');

        $data = array_map(function ($item) {
            return (is_object($item) && method_exists($item, 'toArray')) ?
                $item->toArray() :
                $item;
        }, $data);

        $data = array_merge($data, HandlebarsHelpers::getData());
        $code = LightnCandy::compile($view, $args);
        $code = str_replace('<?php', '', $code);
        $code = str_replace('?>', ';', $code);

        $renderer = eval($code);

        $rendered = $renderer($data);

        $path = realpath($path);

        foreach ($paths as $viewPath) {
            $viewPath = realpath($viewPath);

            if (!Str::endsWith($viewPath, '/')) {
                $viewPath .= '/';
            }

            if (Str::startsWith($path, $viewPath)) {
                $path = substr($path, strlen($viewPath));
                break;
            }
        }

        $pathNoExt = explode('.', $path);
        array_pop($pathNoExt);


        $data['templates'][str_replace('/', '.', implode('.', $pathNoExt))] = $view;

        HandlebarsHelpers::addVars($data);

        return $rendered;
    }


}
