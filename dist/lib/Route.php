<?php
/**
 * lib/Route.php
 *
 * @package OM
 * @author  Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link    https://github.com/lucianolaranjeira/om
 * @version Beta 2.6.2 • Monday, June 24, 2019
 */

namespace lib;

abstract class Route
{
    /**
     * Match...
     *
     *   for instance:
     * 
     *     route = user/{user_id} <<< route variables are defined with {}
     * 
     *     path = user/macgyver
     * 
     *   we'll have (to use through the callback):
     * 
     *     variables = array
     *     (
     *         [user_id] => macgyver
     *     )
     *
     *
     * @param string   $route
     * @param string   $path
     * @param callable $callback (optional)
     * @param mixed    $return   (optional)
     *
     * @return boolean
     */
    public static function match($route, $path, callable $callback = null, &$return = null)
    {
        // Chop, chop, chop...

        $route_slices = explode('/', $route);

        $path_slices = explode('/', $path);

        // Route and Path have the same size?

        if (count($route_slices) == count($path_slices))
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

            if ($route_slices === $path_slices)
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

        // Didn't matches... not this one... not today.

        return false;
    }
}