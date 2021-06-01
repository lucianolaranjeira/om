<?php
/**
 * lib/Request.php (class)
 *
 * @package OM
 * @author  Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link    https://github.com/lucianolaranjeira/om
 * @version Beta 2.7.1 • Monday, May 31, 2021
 */

namespace lib;

abstract class Request
{
    /**
     * The REQUEST will follow the structure below:
     *
     *   GET http://domain.com:00000/myfolder/user/macgyver?firstname=Angus&lastname=MacGyver
     *  |---|------|----------|-----|--------|-------------|---------------------------------|
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
     * Go to another place.
     *
     * @param string $url
     *
     * @return void
     */
    public static function redirect($url): void
    {
        // If is not a valid URL...

        if (!filter_var($url, FILTER_VALIDATE_URL))
        {
            // Maybe it's an internal route, so just try it.

            $url = Request::base() . $url;
        }

        // Go...

        header('Location: ' . $url);
    }

    /**
     * dump.
     *
     * @return void
     */
    public static function dump(): void
    {
        $request = array
        (
            'ip'         => Request::ip()
          , 'ua'         => Request::ua()
          , 'method'     => Request::method()
          , 'protocol'   => Request::protocol()
          , 'domain'     => Request::domain()
          , 'port'       => Request::port()
          , 'path'       => Request::path()
          , 'base'       => Request::base()
          , 'parameters' => Request::parameters()
        );

        print_r($request);
    }

    /**
     * Get IP address.
     *
     * @return string
     */
    public static function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get User Agent string.
     *
     * @return string
     */
    public static function ua(): string
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Get HTTP method.
     *
     * @return string
     */
    public static function method(): string
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
    public static function protocol(): string
    {
        return $_SERVER['REQUEST_SCHEME'];
    }

    /**
     * Get HTTP domain.
     *
     * @return string
     */
    public static function domain(): string
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Get HTTP port.
     *
     * @return string
     */
    public static function port(): string
    {
        return $_SERVER['SERVER_PORT'];
    }

    /**
     * Get path (watch out the app folder).
     *
     * @return string | null
     */
    public static function path(): ?string
    {
        return trim(substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen(Request::$folder)), '/');
    }

    /**
     * Get app url base.
     *
     * @param boolean $explicit
     *
     * @return string
     */
    public static function base($explicit = false): string
    {
        $base = Request::protocol() . '://' . Request::domain();

        if ($explicit)
        {
            $port = Request::port();

            if ($port)
            {
                $base .= ':' . $port;
            }
        }

        return $base . Request::$folder;
    }

    /**
     * Get HTTP parameters.
     *
     * @return array
     */
    public static function parameters(): array
    {
        $method = Request::method();

        switch($method)
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
    private static function parseInputFile(): array
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