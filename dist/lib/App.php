<?php
/**
 * .\lib\App.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 1.0.2 • Tuesday, August 22, 2018
 */

namespace lib;

abstract class App
{
    /**
     * App controllers namespace.
     *
     * @var string $controllers (namespace)
     */
    public static $controllers = 'app\controllers';

    /**
     * HTTP request method.
     *
     * @var string $method
     */
    public static $method;

    /**
     * HTTP request protocol.
     *
     * @var string $protocol
     */
    public static $protocol;

    /**
     * HTTP request domain.
     *
     * @var string $domain
     */
    public static $domain;

    /**
     * HTTP request port.
     *
     * @var string $port
     */
    public static $port;

    /**
     * HTTP request folder.
     *
     * @var string $folder
     */
    public static $folder;

    /**
     * HTTP request path.
     *
     * @var string $path
     */
    public static $path;

    /**
     * HTTP request parameters.
     *
     * @var string $parameters
     */
    public static $parameters = array();

    /**
     * HTTP request base.
     *
     * @var string $base
     */
    public static $base;

    /**
     * User agent browser details.
     *
     * @var array $browser
     */
    public static $browser = array();

    /**
     * Getting things done.
     *
     * @param array $routes
     * @param string $folder (optional)
     *
     * @return void
     */
    public static function run(array $routes, $folder = '/')
    {

        /**
         * The REQUEST follows the structure below:
         *
         *              [8]
         *      |--------------------------------|
         *   GET http://domain.com:00000/myfolder/user/macgyver?firstname=Angus&lastname=MacGyver
         *  |---|------|----------|-----|--------|-------------|---------------------------------|
         *   [1]   [2]     [3]      [4]     [5]        [6]                    [7]
         *
         * For example:
         *
         *    [1] method     = GET
         *    [2] protocol   = HTTP
         *    [3] domain     = domain.com
         *    [4] port       = 00000
         *    [5] folder     = /myfolder/
         *    [6] path       = user/macgyver
         *    [7] parameters = array('firstname' => 'Angus', 'lastname' => 'MacGyver')
         *    [8] base       = http://domain.com:00000/myfolder/
         */

        // Set app folder..

        App::$folder = $folder;

        // Get HTTP request method.

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

        App::$method = $method;

        // Get HTTP request protocol.

        App::$protocol = $_SERVER['REQUEST_SCHEME'];

        // Get HTTP request domain.

        App::$domain = $_SERVER['SERVER_NAME'];

        // Get HTTP request port.

        App::$port = $_SERVER['SERVER_PORT'];

        // Get HTTP request path.

        $path = $_SERVER['REQUEST_URI'];

        if (isset($_SERVER['QUERY_STRING']))
        {
            $query_string = $_SERVER['QUERY_STRING'];

            if ($query_string)
            {
                if (!($query_string[0] === '?'))
                {
                    $query_string = '?' . $query_string;
                }

                $path = rtrim($path, $query_string);
            }
        }

        $path = trim(substr($path, strlen(App::$folder)), '/');

        App::$path = $path;

        // Get HTTP request parameters.

        $parameters = array();

        switch(App::$method)
        {
            case 'GET':

                $parameters = $_GET;

                break;

            case 'POST':

                $parameters = $_POST;

                break;

            case 'PUT':

                $parameters = App::parseInputFile();

                break;

            default:

                // nevermind.

                $parameters = null;

                break;
        }

        App::$parameters = $parameters;

        // Get HTTP request base.

        App::$base = App::$protocol . '://' . App::$domain . ':' . App::$port . App::$folder;

        // Get user agent browser details.

        App::$browser = App::parseUserAgent();

        // Include routes files.

        foreach ($routes as $route)
        {
            if (file_exists($route))
            {
                include $route;
            }
        }
    }

    /**
     * Try match a requested path over a defined route.
     *
     *   For example, with:
     * 
     *     path  = myfolder/user/macgyver
     * 
     *     route = user/{user_id}  <<< route variables are defined with {}
     * 
     *   we'll have:
     * 
     *     route_variables = array
     *     (
     *         [user_id] => macgyver
     *     )
     *
     * @param string $method
     * @param string $route
     * @param string|callable $callback (optional)
     *
     * @return mixed
     */
    public static function match($method, $route, $callback = null)
    {
        if (App::$method === $method)
        {
            /*
             * The regex \{(.*?)\} will find...
             * 
             *   ex:
             *
             *     user/{$user}
             *         |-------| <-- found this...
             * 
             *     $variables will contain:
             *
             *       array
             *       (
             *           [0] => {$user}
             *           [1] => $user
             *       )
             */

            preg_match_all('/\{(.*?)\}/', $route, $variables);

            // match...

            $path = explode('/', App::$path);

            $route = explode('/', $route);

            if (count($path) == count($route))
            {
                $route_variables = array();

                foreach ($route as $key => $value)
                {
                    if (in_array($value, $variables[0]))
                    {
                        $route_variables[trim($value, '{}')] = $path[$key];

                        $path[$key] = $value;
                    }
                }

                if ($path === $route)
                {
                    if ($callback)
                    {
                        // Callback is an explicit function or method.
                        if (is_callable($callback))
                        {
                            return call_user_func_array($callback, $route_variables);
                        }

                        // For an implicit method, try callback as a controller method.

                        $controller = App::$controllers . '\\' .  $callback;

                        if (is_callable($controller))
                        {
                            return call_user_func_array($controller, $route_variables);
                        }
                    }

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Set HTTP response status.
     *
     * @param string $status
     *
     * @return void
     */
    public static function status($status = '200 OK')
    {

        /*
         * Status code registry, see the IANA list:
         *
         *    https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
         *
         * Good view, see this diagram:
         *
         *    https://github.com/for-GET/http-decision-diagram/blob/master/doc/README.md
         */

        header('HTTP/1.1 ' . $status);
    }

    /**
     * Set HTTP response content type and charset.
     *
     * @param array $mime
     * @param array $charset (optional, default utf-8)
     *
     * @return void
     */
    public static function content($mime, $charset = 'utf-8')
    {
        /*
         * Media types (MIME), see IANA list:
         *
         *   https://www.iana.org/assignments/media-types/media-types.xhtml
         */

        header('Content-Type: ' . $mime . '; charset=' . $charset);
    }

    /**
     * Load a file content.
     *
     * @param  string $filename
     * @param  array $variables (optional)
     *
     * @return boolean
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

            return true;
        }

        return false;
    }

    /**
     * Go to another place.
     *
     * @param string $url
     *
     * @return void
     */
    public static function redirect($url)
    {
        header('Location: ' . $url);
    }

    /**
     * Bypass to an internal route.
     *
     * @param string $path
     *
     * @return void
     */
    public static function bypass($path)
    {
        $url = App::$base . $path;

        App::redirect($url);
    }

    /**
     * All done.
     *
     * @return void
     */
    public static function end()
    {
        exit;
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

    /**
     * Get user agent browser details.
     *
     * @return array
     */
    private static function parseUserAgent()
    {
        // Get IP address.

        $address = $_SERVER['REMOTE_ADDR'];

        // Get browser details.

        $platform = null;
        $browser  = null;
        $version  = null;

        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;

        if ($user_agent)
        {
            if (preg_match('/\((.*?)\)/im', $user_agent, $parent_matches))
            {
                preg_match_all('/(?P<platform>BB\d+;|Android|CrOS|Tizen|iPhone|iPad|iPod|Linux|Macintosh|Windows(\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|(New\ )?Nintendo\ (WiiU?|3?DS)|Xbox(\ One)?)(?:\ [^;]*)?(?:;|$)/imx', $parent_matches[1], $result, PREG_PATTERN_ORDER);

                $priority = array('Xbox One', 'Xbox', 'Windows Phone', 'Tizen', 'Android');

                $result['platform'] = array_unique($result['platform']);

                if (count($result['platform']) > 1)
                {
                    if ($keys = array_intersect($priority, $result['platform']))
                    {
                        $platform = reset($keys);
                    }
                    else
                    {
                        $platform = $result['platform'][0];
                    }
                }
                elseif (isset($result['platform'][0]))
                {
                    $platform = $result['platform'][0];
                }
            }

            if ($platform == 'linux-gnu')
            {
                $platform = 'Linux';
            }
            elseif ($platform == 'CrOS')
            {
                $platform = 'Chrome OS';
            }

            preg_match_all('%(?P<browser>Camino|Kindle(\ Fire)?|Firefox|Iceweasel|Safari|MSIE|Trident|AppleWebKit|TizenBrowser|Chrome|Vivaldi|IEMobile|Opera|OPR|Silk|Midori|Edge|CriOS|Baiduspider|Googlebot|YandexBot|bingbot|Lynx|Version|Wget|curl|NintendoBrowser|PLAYSTATION\ (\d|Vita)+)(?:\)?;?)(?:(?:[:/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%ix', $user_agent, $result, PREG_PATTERN_ORDER);

            if ((!isset($result['browser'][0])) or (!isset($result['version'][0])))
            {
                if (preg_match('%^(?!Mozilla)(?P<browser>[A-Z0-9\-]+)(/(?P<version>[0-9A-Z.]+))?%ix', $user_agent, $result))
                {
                    $browser = $result['browser'];
                    $version = isset($result['version']) ? $result['version'] : null;
                }
            }
            else
            {
                if (preg_match('/rv:(?P<version>[0-9A-Z.]+)/si', $user_agent, $rv_result))
                {
                    $rv_result = $rv_result['version'];
                }

                $browser = $result['browser'][0];

                $version = $result['version'][0];

                $lowerBrowser = array_map('strtolower', $result['browser']);

                $find = function($search, &$key) use ($lowerBrowser)
                {
                    $xkey = array_search(strtolower($search), $lowerBrowser);

                    if($xkey !== false)
                    {
                        $key = $xkey;

                        return true;
                    }

                    return false;
                };

                $key  = 0;

                $ekey = 0;

                if ($browser == 'Iceweasel')
                {
                    $browser = 'Firefox';
                }
                elseif ($find('Playstation Vita', $key))
                {
                    $platform = 'PlayStation Vita';

                    $browser = 'Browser';
                }
                elseif (($find('Kindle Fire', $key)) or ($find('Silk', $key)))
                {
                    $browser = $result['browser'][$key] == 'Silk' ? 'Silk' : 'Kindle';

                    $platform = 'Kindle Fire';

                    if ((!($version = $result['version'][$key])) or (!is_numeric($version[0])))
                    {
                        $version = $result['version'][array_search('Version', $result['browser'])];
                    }
                }
                elseif (($find('NintendoBrowser', $key)) or ($platform == 'Nintendo 3DS'))
                {
                    $browser = 'NintendoBrowser';

                    $version = $result['version'][$key];
                }
                elseif ($find('Kindle', $key))
                {
                    $browser  = $result['browser'][$key];

                    $platform = 'Kindle';

                    $version  = $result['version'][$key];
                }
                elseif ($find('OPR', $key))
                {
                    $browser = 'Opera Next';

                    $version = $result['version'][$key];
                }
                elseif ($find('Opera', $key))
                {
                    $browser = 'Opera';

                    $find('Version', $key);

                    $version = $result['version'][$key];
                }
                elseif ($find('Midori', $key))
                {
                    $browser = 'Midori';

                    $version = $result['version'][$key];
                }
                elseif (($browser == 'MSIE') or ($rv_result and ($find('Trident', $key))) or ($find('Edge', $ekey)))
                {
                    $browser = 'MSIE';

                    if ($find('IEMobile', $key))
                    {
                        $browser = 'IEMobile';

                        $version = $result['version'][$key];
                    }
                    elseif ($ekey)
                    {
                        $version = $result['version'][$ekey];
                    }
                    else
                    {
                        $version = $rv_result ?: $result['version'][$key];
                    }

                    if (version_compare($version, '12', '>='))
                    {
                        $browser = 'Edge';
                    }
                }
                elseif ($find('Vivaldi', $key))
                {
                    $browser = 'Vivaldi';

                    $version = $result['version'][$key];
                }
                elseif (($find('Chrome', $key)) or ($find('CriOS', $key)))
                {
                    $browser = 'Chrome';

                    $version = $result['version'][$key];
                }
                elseif ($browser == 'AppleWebKit')
                {
                    if (($platform == 'Android') and (!($key = 0)))
                    {
                        $browser = 'Android Browser';
                    }
                    elseif (strpos($platform, 'BB') === 0)
                    {
                        $browser  = 'BlackBerry Browser';

                        $platform = 'BlackBerry';
                    }
                    elseif (($platform == 'BlackBerry') or ($platform == 'PlayBook'))
                    {
                        $browser = 'BlackBerry Browser';
                    }
                    elseif ($find('Safari', $key))
                    {
                        $browser = 'Safari';
                    }
                    elseif ($find('TizenBrowser', $key))
                    {
                        $browser = 'TizenBrowser';
                    }

                    $find('Version', $key);

                    $version = $result['version'][$key];
                }
                elseif ($key = preg_grep('/playstation \d/i', array_map('strtolower', $result['browser'])))
                {
                    $key = reset($key);

                    $platform = 'PlayStation ' . preg_replace('/[^\d]/i', '', $key);

                    $browser = 'NetFront';
                }
            }
        }
 
        // Return browser details.
        return array
        (
            'address'  => $address
          , 'platform' => $platform
          , 'browser'  => $browser
          , 'version'  => $version
        );
    }

    /**
     * Get App details.
     *
     * @return string
     */    
    public static function details()
    {
        return json_encode
        (
            array
            (
                'method'     => App::$method
              , 'protocol'   => App::$protocol
              , 'domain'     => App::$domain
              , 'port'       => App::$port
              , 'folder'     => App::$folder
              , 'path'       => App::$path
              , 'parameters' => App::$parameters
              , 'base'       => App::$base
              , 'browser'    => App::$browser
            )

          , JSON_PRETTY_PRINT
        );
    }

    /**
     * Register autoload function.
     *
     * @return boolean
     */    
    public static function register()
    {
        return spl_autoload_register
        (
            function($class)
            {
                $slash = DIRECTORY_SEPARATOR;

                $filename = '..' . $slash . str_replace('\\', $slash, $class) . '.php';

                if (file_exists($filename))
                {
                    require $filename;

                    return true;
                }

                return false;
            }
        );
    }
}

App::register();
