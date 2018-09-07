<?php
/**
 * .\app\index.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 1.0.3 • Tuesday, September 7, 2018
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

  , '/'

);

// If any route wasn't found (or didn,t something) this is the right time to say something, isn't?

App::redirect('301 Permanent Redirect', 'notfound');