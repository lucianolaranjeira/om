<?php
/**
 * ./om/dist/app/data/Site.php
 *
 * @package OM
 * @author  Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link    https://github.com/lucianolaranjeira/om
 * @version Beta 2.6.1 • Sunday, March 3, 2019
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
    public static function url($path = '')
    {
        return Request::base() . $path;
    }
}