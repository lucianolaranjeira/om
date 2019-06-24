<?php
/**
 * lib/File.php (class)
 *
 * @package OM
 * @author  Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link    https://github.com/lucianolaranjeira/om
 * @version Beta 2.7.0 • Monday, June 24, 2019
 */

namespace lib;

abstract class File
{
    /**
     * Load a file content.
     *
     * @param string $filename
     * @param array  $variables (optional)
     *
     * @return void
     */
    public static function load($filename, array $variables = null): void
    {
        if (file_exists($filename))
        {
            if (!empty($variables))
            {
                extract($variables);
            }

            include $filename;
        }
    }
}