<?php
/**
 * .\lib\Response.php
 *
 * @package    OM
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.4.0 • Wednesday, December 19, 2018
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
    public static function status($status)
    {
        /*
           Status codes, see the IANA list:
          
              https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
          
           Good view, see this diagram:
          
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
    public static function content($mime, $charset)
    {
        /*
           Media types (MIME), see IANA list:
          
             https://www.iana.org/assignments/media-types/media-types.xhtml
         */

        header('Content-Type: ' . $mime . '; charset=' . $charset);
    }
}