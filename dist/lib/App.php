<?php
/**
 * .\lib\App.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 1.0.3 • Tuesday, September 7, 2018
 */

namespace lib;

abstract class App
{
    /**
     * App controllers namespace.
     *
     * @var string $controllers (namespace)
     */
    private static $controllers = 'app\controllers';

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

    /**
     * HTTP request method.
     *
     * @var string $method
     */
    private static $method;

    /**
     * HTTP request protocol.
     *
     * @var string $protocol
     */
    private static $protocol;

    /**
     * HTTP request domain.
     *
     * @var string $domain
     */
    private static $domain;

    /**
     * HTTP request port.
     *
     * @var string $port
     */
    private static $port;

    /**
     * HTTP request folder.
     *
     * @var string $folder
     */
    private static $folder = '/';

    /**
     * HTTP request path.
     *
     * @var string $path
     */
    private static $path;

    /**
     * HTTP request parameters.
     *
     * @var string $parameters
     */
    private static $parameters = array();

    /**
     * HTTP request base.
     *
     * @var string $base
     */
    private static $base;

    /**
     * User agent browser details.
     *
     * @var array $browser
     */
    private static $browser = array();

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
        // Set app folder.

        App::setFolder($folder);

        // Set app request method.

        App::setMethod();

        // Set app request protocol.

        App::setProtocol();

        // Set app request domain.

        App::setDomain();

        // Set app request port.

        App::setPort();

        // Set app request path.

        App::setPath();

        // Set app request parameters.

        App::setParameters();

        // Set app request base.

        App::setBase();

        // Set app request browser.

        App::setBrowser();

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
     * Set App folder.
     *
     * @param string $folder (optional)
     *
     * @return string
     */
    private static function setFolder($folder = '/')
    {
        App::$folder = $folder;
    }

    /**
     * Set App method.
     *
     * @param string $method
     *
     * @return string
     */
    private static function setMethod($method = null)
    {
        if ($method)
        {
            $_method = $method;
        }
        else
        {
            // Get HTTP request method.

            $_method = $_SERVER['REQUEST_METHOD'];

            if (($_method == 'POST') and (array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)))
            {
                if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE')
                {
                    $_method = 'DELETE';
                }
                else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT')
                {
                    $_method = 'PUT';
                }
                else
                {
                    $_method = null;
                }
            }
        }

        App::$method = $_method;

        return App::$method;
    }

    /**
     * Set App protocol.
     *
     * @param string $protocol (optional)
     *
     * @return string
     */
    private static function setProtocol($protocol = null)
    {
        if ($protocol)
        {
            $_protocol = $protocol;
        }
        else
        {
            // Get HTTP request protocol.

            $_protocol = $_SERVER['REQUEST_SCHEME'];
        }

        App::$protocol = $_protocol;

        return App::$protocol;
    }

    /**
     * Set App domain.
     *
     * @param string $domain (optional)
     *
     * @return string
     */
    private static function setDomain($domain = null)
    {
        if ($domain)
        {
            $_domain = $domain;
        }
        else
        {
            // Get HTTP request domain.

            $_domain = $_SERVER['SERVER_NAME'];
        }

        App::$domain = $_domain;

        return App::$domain;
    }

    /**
     * Set App port.
     *
     * @param string $port (optional)
     *
     * @return string
     */
    private static function setPort($port = null)
    {
        if ($port)
        {
            $_port = $port;
        }
        else
        {
            // Get HTTP request port.

            $_port = $_SERVER['SERVER_PORT'];
        }

        App::$port = $_port;

        return App::$port;
    }

    /**
     * Set App path.
     *
     * @param string $path (optional)
     *
     * @return string
     */
    private static function setPath($path = null)
    {
        if ($path)
        {
            $_path = $path;
        }
        else
        {
            // Get HTTP request path.

            $_path = $_SERVER['REQUEST_URI'];

            if (isset($_SERVER['QUERY_STRING']))
            {
                $query_string = $_SERVER['QUERY_STRING'];

                if ($query_string)
                {
                    if (!($query_string[0] === '?'))
                    {
                        $query_string = '?' . $query_string;
                    }

                    $_path = rtrim($_path, $query_string);
                }
            }

            $_path = trim(substr($_path, strlen(App::$folder)), '/');
        }

        App::$path = $_path;

        return App::$path;
    }

    /**
     * Set App parameters.
     *
     * @param array $parameters (optional)
     *
     * @return array
     */
    private static function setParameters(array $parameters = null)
    {
        if ($parameters)
        {
            $_parameters = $parameters;
        }
        else
        {
            // Get HTTP request parameters.

            switch(App::$method)
            {
                case 'GET':

                    $_parameters = $_GET;

                    break;

                case 'POST':

                    $_parameters = $_POST;

                    break;

                case 'PUT':

                    $_parameters = App::parseInputFile();

                    break;

                default:

                    // nevermind.

                    $_parameters = array();

                    break;
            }
        }

        App::$parameters = $_parameters;

        return App::$parameters;
    }

    /**
     * Set App base.
     *
     * @param string $base (optional)
     *
     * @return string
     */
    private static function setBase($base = null)
    {
        if ($base)
        {
            $_base = $base;
        }
        else
        {
            // Get HTTP request base.

            $_base = App::$protocol . '://' . App::$domain . ':' . App::$port . App::$folder;
        }

        App::$base = $_base;

        return App::$base;
    }

    /**
     * Set App browser.
     *
     * @param array $browser (optional)
     *
     * @return string
     */
    private static function setBrowser(array $browser = null)
    {
        if ($browser)
        {
            $_browser = $browser;
        }
        else
        {
            // Get user agent browser details.

            $_browser = App::parseUserAgent();
        }

        App::$browser = $_browser;

        return App::$browser;
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
     * @param string $visibility
     * @param string $method
     * @param string $route
     * @param string|callable $callback (optional)
     *
     * @return mixed
     */
    public static function match($visibility, $method, $route, $callback = null)
    {
        // Check the requested method...

        if (App::$method === $method)
        {
            // Check visibility and do sometyhing (or not) about access and/or authentication...

            switch($visibility)
            {
                case 'PUBLIC':

                    $authorized = true;

                    break;

                case 'PRIVATE':

                    // Here, use some kind of authentication algorithm.

                    $authorized = false;

                    break;

                case 'BLOCKED':

                    $authorized = false;

                    break;

                default:

                    // Never trust if the visibility wasn't defined.

                    $authorized = false;

                    break;
            }

            // If access granted, try match the route...

            if ($authorized)
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
        }

        return false;
    }

    /**
     * App response.
     *
     * @param string $status
     * @param array $mime
     * @param array $charset
     * @param string $filename (optional)
     * @param array $variables (optional)
     *
     * @return void
     */
    public static function response($status, $mime, $charset, $filename = null, array $variables = null)
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

        /*
         * Media types (MIME), see IANA list:
         *
         *   https://www.iana.org/assignments/media-types/media-types.xhtml
         */

        header('Content-Type: ' . $mime . '; charset=' . $charset);

        // Load content...

        if ($filename)
        {
            App::load($filename, $variables);
        }
    }

    /**
     * Load a file content.
     *
     * @param string $filename
     * @param array $variables (optional)
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
     * @param string $status
     * @param string $url
     *
     * @return void
     */
    public static function redirect($status, $url)
    {
        // If is not a valid URL, it's supposed to be a internal route, so just try it.

        if (!filter_var($url, FILTER_VALIDATE_URL))
        {
            $url = App::$base . $url;
        }

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

        // Go...

        header('Location: ' . $url);
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
