<?php
/**
 * lib/Autoload.php (class)
 *
 * @package OM
 * @author  Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link    https://github.com/lucianolaranjeira/om
 * @version Beta 2.7.1 • Monday, May 31, 2021
 */

namespace lib;

abstract class Autoload
{
    /**
     * Register.
     *
     * @return bool
     */    
    public static function register(): bool
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