<?php
/**
 * Handlebars extension to the Laravel View class.
 *
 * Loads and renders handlebars templates, and stores
 * the templates in memory for inclusion in source files
 * using the JsVars class.
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
use Handlebars\Handlebars;
use Weyforth\JS\JsVar;

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
     * unrendered template in JsVar container for inclusion later on.
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
        $h    = new Handlebars();

        $h->addHelper('trans', function($template, $context, $var, $ff){
            return trans(substr($var, 1, -1));
        });

        $paths = Config::get('view.paths');

        $data = array_map(function ($item) {
            return (is_object($item) && method_exists($item, 'toArray')) ?
                $item->toArray() :
                $item;
        }, $data);

        $standardData = array(
            'errors' => Session::has('errors') ? Session::get('errors')->all() : null,
            'error' => Session::has('errors') ? Session::get('errors')->first() : null,
            'message' => Session::has('message') ? Session::get('message') : null,
            'input' => Session::has('input') ? Session::get('input') : null,
        );

        $data = array_merge($data, $standardData);

        $rendered = $h->render($view, $data);

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

        JsVar::container('templates')->add(str_replace('/', '.', implode('.', $pathNoExt)), $view);

        return $rendered;
    }


}
