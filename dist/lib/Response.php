<?php
/**
 * lib/Response.php (class)
 *
 * @package OM
 * @author  Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link    https://github.com/lucianolaranjeira/om
 * @version Beta 2.7.0 • Monday, June 24, 2019
 */

namespace lib;

abstract class Response
{
    /**
     * Set status.
     *
     * @param string $status
     *
     * @return void
     */
    public static function status($status): void
    {
        /*
           Status codes:
          
              https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
          
           Diagram:
          
              https://github.com/for-GET/http-decision-diagram/blob/master/doc/README.md
          
         */

        header('HTTP/1.1 ' . $status);
    }

    /**
     * Set content type.
     *
     * @param array $mime
     * @param array $charset
     *
     * @return void
     */
    public static function content($mime, $charset): void
    {
        /*
           Media types (MIME):
          
             https://www.iana.org/assignments/media-types/media-types.xhtml
         */

        header('Content-Type: ' . $mime . '; charset=' . $charset);
    }
}