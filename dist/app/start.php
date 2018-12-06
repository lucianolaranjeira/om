<?php
/**
 * .\app\start.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.0.0 • Thursday, December 6, 2018
 */

// Let's get our hands dirty!

require '../lib/Autoload.php';

// Hey Dorothy! Show me the yellow brick road.

use lib\App;

App::run
(
    // Routes.

    array
    (
        '../app/routes/site.php'
    )

    // Application folder.

  , '/'

);

// If a route wasn't found this is the right time to say something, isn't?

if (!App::$route)
{
    App::redirect('301 Permanent Redirect', 'notfound');
}