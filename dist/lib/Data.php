<?php
/**
 * lib/Data.php (class)
 *
 * @package OM
 * @author  Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link    https://github.com/lucianolaranjeira/om
 * @version Beta 2.7.1 • Monday, May 31, 2021
 */

namespace lib;

abstract class Data
{
    /**
     * Select.
     *
     * @param array $array
     * @param array $filters (optional)
     *
     * @return array
     */
    public static function select(array $array, array $filters = array()): array
    {
        $result = array();

        foreach ($array as $id => $data)
        {
            $include = true;

            if (!empty($filters))
            {
                foreach ($filters as $key => $value)
                {
                    if (is_array($data[$key]))
                    {
                        if (!in_array($value, $data[$key]))
                        {
                            $include = false;
                        }
                    }
                    else
                    {
                        if ($data[$key] !== $value)
                        {
                            $include = false;                            
                        }
                    }

                    if (!$include)
                    {
                        break;
                    }
                }
            }

            if ($include)
            {
                $result[$id] = $data;
            }
        }

        return $result;
    }
}