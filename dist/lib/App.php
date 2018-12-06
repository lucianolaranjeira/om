<?php
/**
 * .\lib\App.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.0.0 • Thursday, December 6, 2018
 */

namespace lib;

abstract class App
{
    /**
     * Folder application root.
     *
     * @var string $folder
     */
    public static $folder = '/';

    /**
     * Found route.
     *
     * @var string $route
     */
    public static $route;

    /**
     * Getting things done.
     *
     * @param array  $routes
     * @param string $folder (optional)
     *
     * @return void
     */
    public static function run(array $routes, $folder = '/')
    {
        // Set app folder.

        App::$folder = $folder;

        // Include routes...

        foreach ($routes as $route)
        {
            if (file_exists($route))
            {
                include $route;
            }
        }
    }

    /**
     * Try to match the requested path over a defined route.
     *
     *   For instance:
     * 
     *     path = myfolder/user/macgyver
     * 
     *     route = user/{user_id}  <<< route variables are defined with {}
     * 
     *   We'll have:
     * 
     *     route_variables = array
     *     (
     *         [user_id] => macgyver
     *     )
     *
     * @param string   $method
     * @param string   $visibility
     * @param string   $route
     * @param callable $callback   (optional)
     *
     * @return mixed
     */
    public static function match($method, $visibility, $route, callable $callback = null)
    {
        // Check if some route has been found before...

        if (!App::$route)
        {
            // Check route method...

            if (Request::method() === $method)
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

                // If access granted...

                if ($authorized)
                {
                    // Let's try match the route...

                    $path_slices = explode('/', Request::path());

                    $route_slices = explode('/', $route);

                    if (count($path_slices) == count($route_slices))
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

                        $route_variables = array();

                        foreach ($route_slices as $key => $value)
                        {
                            if (in_array($value, $variables[0]))
                            {
                                $route_variables[trim($value, '{}')] = $path_slices[$key];

                                $path_slices[$key] = $value;
                            }
                        }

                        // When matched, assume that this route has been found...

                        if ($path_slices === $route_slices)
                        {
                            // Yeap. Route was found.

                            App::$route = $route;

                            if ($callback)
                            {
                                return call_user_func_array($callback, $route_variables);
                            }

                            return true;
                        }
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
     * @param array  $mime
     * @param array  $charset
     * @param string $filename  (optional)
     * @param array  $variables (optional)
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

    /**
     * Site URL.
     *
     * @var string $path (optional)
     *
     * @return string
     */
    public static function url($path = null)
    {
        return Request::base() . $path;
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
        // If is not a valid URL, It's supposed to be an internal route, so just try it.

        if (!filter_var($url, FILTER_VALIDATE_URL))
        {
            $url = App::url($url);
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
}