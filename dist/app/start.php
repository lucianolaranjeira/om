<?php
/**
 * .\app\start.php
 *
 * @package    OM
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.3.1 • Tuesday, December 18, 2018
 */

// Okay, Dorothy, let's get our hands dirty... so show me the yellow brick road!

require '../lib/Autoload.php';

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
    Response::status('308 Permanent Redirect');

    App::redirect('notfound');
}