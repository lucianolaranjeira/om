<?php
/**
 * .\app\routes\site.php
 *
 * @package    OM
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.3.1 • Tuesday, December 18, 2018
 */

use lib\App;

// Not Found (page). IMPORTANT! Never ever forget this route.

App::match
(
    'GET', 'PUBLIC', 'notfound'

  , function()
    {
        Response::status('404 Not Found');

        Response::content('text/html', 'utf-8');

        App::load('../app/interface/notfound.phtml');
    }
);

// Index.

App::match
(
    'GET', 'PUBLIC', ''

  , function()
    {
        Response::status('308 Permanent Redirect');

        App::redirect('home');
    }
);

// Home (page).

App::match
(
    'GET', 'PUBLIC', 'home'

  , function()
    {
        Response::status('200 OK');

        Response::content('text/html', 'utf-8');

        App::load('../app/interface/home.phtml');
    }
);

// GitHub.

App::match
(
    'GET', 'PUBLIC', 'github'

  , function()
    {
        Response::status('308 Permanent Redirect');

        App::redirect('https://github.com/lucianolaranjeira/om');
    }
);