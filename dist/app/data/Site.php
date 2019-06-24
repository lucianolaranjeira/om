<?php
/**
 * app/data/Site.php (class)
 *
 * @package OM
 * @author  Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link    https://github.com/lucianolaranjeira/om
 * @version Beta 2.7.0 • Monday, June 24, 2019
 */

namespace app\data;

use lib\Request;

abstract class Site
{
    /**
     * Get URL.
     *
     * @param string $path (optional)
     *
     * @return string
     */
    public static function url($path = ''): string
    {
        return Request::base() . $path;
    }
}