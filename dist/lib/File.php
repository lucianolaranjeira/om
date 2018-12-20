<?php
/**
 * .\lib\File.php
 *
 * @package    OM
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.4.0 • Wednesday, December 19, 2018
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
    public static function load($filename, array $variables = null)
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