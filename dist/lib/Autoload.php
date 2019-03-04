<?php
/**
 * ./om/dist/lib/Autoload.php
 *
 * @package OM
 * @author  Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link    https://github.com/lucianolaranjeira/om
 * @version Beta 2.6.1 • Sunday, March 3, 2019
 */

namespace lib;

abstract class Autoload
{
    /**
     * Register.
     *
     * @return boolean
     */    
    public static function register()
    {
        return spl_autoload_register
        (
            function($class)
            {
                require '../' . str_replace('\\', '/', $class) . '.php';
            }
        );
    }
}

Autoload::register();