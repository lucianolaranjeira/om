<?php
/**
 * .\app\index.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 1.0.0 • Tuesday, August 21, 2018
 */

/*
 |--------------------------------------------------------------------------
 | Let's get our hands dirty!
 |--------------------------------------------------------------------------
 |
 | Hey Dorothy! Show me the yellow brick road.
 |
 */

require '../lib/App.php';

use lib\app;

App::run
(
    // Routes (files).

    array
    (
        '../app/routes/site.php'
    )

    // Application folder.

  , '/projects/om/'

);

// If any route wasn't found (or didn,t something) this is the right time to say something, isn't?

App::status('404 Not Found');

App::end();