<?php
/**
 * .\lib\App.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.2.0 • Wednesday, December 12, 2018
 */

namespace lib;

abstract class App
{
    /**
     * The route that was matched.
     *
     * @var string $route
     */
    public static $route;

    /**
     * Hey! Ho! Let's go!.
     *
     * @param array $routes
     * @param array $folder (optional)
     *
     * @return void
     */
    public static function run(array $routes, $folder = '/')
    {
        // Application folder.

        Request::$folder = $folder;

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
                           The regex \{(.*?)\} will find...
                           
                             ex:
                          
                               user/{$user}
                                   |-------| <-- found this...
                           
                               $variables will contain:
                          
                                 array
                                 (
                                     [0] => {$user}
                                     [1] => $user
                                 )
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
     * Go to another place.
     *
     * @param string $url
     *
     * @return void
     */
    public static function redirect($url)
    {
        // If is not a valid URL, It's supposed to be an internal route, so just try it.

        if (!filter_var($url, FILTER_VALIDATE_URL))
        {
            $url = Request::base() . $url;
        }

        // Go...

        header('Location: ' . $url);
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
}