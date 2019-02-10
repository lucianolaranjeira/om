<?php
/**
 * .\lib\Site.php
 *
 * @package    OM
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.4.1 • Saturday, February 9, 2019
 */

namespace lib;

abstract class Site
{
    /**
     * Get URL.
     *
     * @param string $path
     *
     * @return string
     */
    public static function url($path)
    {
        return Request::base() . $path;
    }
}