<?php
/**
 * .\lib\Autoload.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.2.0 • Wednesday, December 12, 2018
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