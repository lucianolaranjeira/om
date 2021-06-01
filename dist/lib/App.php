<?php
/**
 * lib/App.php (class)
 *
 * @package OM
 * @author  Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link    https://github.com/lucianolaranjeira/om
 * @version Beta 2.7.1 • Monday, May 31, 2021
 */

namespace lib;

abstract class App
{
    /**
     * Build up a URL.
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