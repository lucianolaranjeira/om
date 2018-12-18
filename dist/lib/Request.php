<?php
/**
 * .\lib\Request.php
 *
 * @package    OM
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.3.1 • Tuesday, December 18, 2018
 */

namespace lib;

abstract class Request
{
    /**
     * The REQUEST will follow the structure below:
     *
     *   GET http://domain.com:00000/myfolder/user/macgyver?firstname=Angus&lastname=MacGyver
     *  |---|------|----------|-----|--------|--------------|---------------------------------|
     *   [1]   [2]     [3]      [4]    [5]         [6]                    [7]
     *
     * For example:
     *
     *    [1] method       = GET
     *    [2] protocol     = HTTP
     *    [3] domain       = domain.com
     *    [4] port         = 00000
     *    [5] folder (app) = myfolder
     *    [6] path         = user/macgyver
     *    [7] parameters   = array('firstname' => 'Angus', 'lastname' => 'MacGyver')
     */

    /**
     * Application folder.
     *
     * @var string $folder
     */
    public static $folder = '/';

    /**
     * Get IP address.
     *
     * @return string
     */
    public static function ip()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get User Agent string.
     *
     * @return string
     */
    public static function ua()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Parse the user agent string to get the client screen (mobile, tablet, desktop, etc).
     *
     * @return string
     */
    public static function screen()
    {
        $ua = Request::ua();

        // Look for tablets first...

        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobile))/i', $ua))
        {
            $screen = 'tablet';
        }

        // ...then mobile...

        elseif (preg_match('/Mobi|iP(hone|od)|Android|BlackBerry/', $ua))
        {
            $screen = 'mobile';
        }

        // or anything else, consider as a desktop.

        else
        {
            $screen = 'desktop';
        }

        return $screen;
    }

    /**
     * Get HTTP method.
     *
     * @return string
     */
    public static function method()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if (($method == 'POST') and (array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)))
        {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE')
            {
                $method = 'DELETE';
            }
            else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT')
            {
                $method = 'PUT';
            }
            else
            {
                $method = null;
            }
        }

        return $method;
    }

    /**
     * Get HTTP protocol.
     *
     * @return string
     */
    public static function protocol()
    {
        return $_SERVER['REQUEST_SCHEME'];
    }

    /**
     * Get HTTP domain.
     *
     * @return string
     */
    public static function domain()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Get HTTP port.
     *
     * @return string
     */
    public static function port()
    {
        return $_SERVER['SERVER_PORT'];
    }

    /**
     * Get path (consider the app folder).
     *
     * @return string
     */
    public static function path()
    {
        return trim(substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen(Request::$folder)), '/');
    }

    /**
     * Get app url base.
     *
     * @return string
     */
    public static function base()
    {
        $base = Request::protocol() . '://' . Request::domain();

        $port = Request::port();

        if ($port)
        {
            $base .= ':' . $port;
        }

        return $base . Request::$folder;
    }

    /**
     * Get HTTP parameters.
     *
     * @return array
     */
    public static function parameters()
    {
        switch(Request::$method)
        {
            case 'GET':

                $parameters = $_GET;

                break;

            case 'POST':

                $parameters = $_POST;

                break;

            case 'PUT':

                $parameters = Request::parseInputFile();

                break;

            default:

                // nevermind.

                $parameters = array();

                break;
        }

        return $parameters;
    }

    /**
     * Parse "php://input" file.
     *
     * @return array
     */
    private static function parseInputFile()
    {
        $input = fopen("php://input", "r");

        $raw_data = '';

        while ($chunk = fread($input, 1024))
        {
            $raw_data .= $chunk;
        }

        fclose($input);

        $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

        if(empty($boundary))
        {
            parse_str($raw_data, $data);
        }
        else
        {
            $parts = array_slice(explode($boundary, $raw_data), 1);

            $data = array();

            foreach ($parts as $part)
            {
                if ($part == "--\r\n")
                {
                    break;
                }

                $part = ltrim($part, "\r\n");

                list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

                $raw_headers = explode("\r\n", $raw_headers);

                $headers = array();

                foreach ($raw_headers as $header)
                {
                    list($name, $value) = explode(':', $header);

                    $headers[strtolower($name)] = ltrim($value, ' ');
                }

                if (isset($headers['content-disposition']))
                {
                    $filename = null;

                    $tmp_name = null;

                    preg_match
                    (
                        '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/'
                      , $headers['content-disposition']
                      , $matches
                    );

                    list(, $type, $name) = $matches;

                    if (isset($matches[4]))
                    {
                        if (isset($_FILES[$matches[2]]))
                        {
                            continue;
                        }

                        $filename = $matches[4];

                        $filename_parts = pathinfo($filename);

                        $tmp_name = tempnam(ini_get('upload_tmp_dir'), $filename_parts['filename']);

                        $_FILES[$matches[2]] = array
                        (
                            'error'    => 0
                          , 'name'     => $filename
                          , 'tmp_name' => $tmp_name
                          , 'size'     => strlen($body)
                          , 'type'     => $value
                        );

                        file_put_contents($tmp_name, $body);
                    }
                    else
                    {
                        $data[$name] = substr($body, 0, strlen($body) - 2);
                    }
                }
            }
        }

        return $data;
    }
}