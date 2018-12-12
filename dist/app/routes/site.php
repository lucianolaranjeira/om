<?php
/**
 * .\app\routes\site.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.2.0 • Wednesday, December 12, 2018
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