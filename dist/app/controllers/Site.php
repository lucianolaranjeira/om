<?php
/**
 * .\app\controllers\Site.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 1.0.3 • Tuesday, September 7, 2018
 */

namespace app\controllers;

use lib\App;

abstract class Site
{
    /**
     * Get home page.
     *
     * @return void
     */
    public static function secret()
    {
        echo 'Silence is golden';

        App::end();
    }
}