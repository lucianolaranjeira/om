<?php
/**
 * ./om/dist/lib/Route.php
 *
 * @package    OM
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.5.3 • Sunday, February 10, 2019
 */

namespace lib;

abstract class Route
{
    /**
     * Match...
     *
     *   For instance:
     * 
     *     path = user/macgyver
     * 
     *     route = user/{user_id}  <<< route variables are defined with {}
     * 
     *   We'll have:
     * 
     *     variables = array
     *     (
     *         [user_id] => macgyver
     *     )
     *
     *   This will be used through the callback.
     *
     * @param string   $path
     * @param string   $route
     * @param callable $callback   (optional)
     * @param mixed    $return     (optional)
     *
     * @return boolean
     */
    public static function match($path, $route, callable $callback = null, &$return = null)
    {
        // Chop, chop, chop...

        $path_slices = explode('/', $path);

        $route_slices = explode('/', $route);

        // Route and Path have the same size?

        if (count($path_slices) == count($route_slices))
        {
            $variables = array();

            // Get the route variables...

            if (preg_match_all('/\{(.*?)\}/', $route, $route_variables) > 0)
            {
                foreach ($route_slices as $key => $value)
                {
                    if (in_array($value, $route_variables[0]))
                    {
                        $variables[trim($value, '{}')] = $path_slices[$key];

                        // Override to match...

                        $path_slices[$key] = $value;
                    }
                }
            }

            // Everything's fine?

            if ($path_slices === $route_slices)
            {
                // Yeap. Route matches.

                if ($callback)
                {
                    $return = call_user_func_array($callback, $variables);
                }

                // No callback... doesn't matter.

                return true;
            }
        }

        // Didn't matches, not this one, not today.

        return false;
    }
}