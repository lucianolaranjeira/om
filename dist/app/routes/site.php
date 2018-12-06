<?php
/**
 * .\app\routes\site.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.0.0 • Thursday, December 6, 2018
 */

use lib\App;

// Not Found (page). IMPORTANT! Never ever forget this route.

App::match
(
    'GET', 'PUBLIC', 'notfound'

  , function()
    {
        App::response('404 Not Found', 'text/html', 'utf-8', '../app/interface/notfound.phtml');
    }
);

// Index.

App::match
(
    'GET', 'PUBLIC', ''

  , function()
    {
        App::redirect('301 Permanent Redirect', 'home');
    }
);

// Home (page).

App::match
(
    'GET', 'PUBLIC', 'home'

  , function()
    {
        App::response('200 OK', 'text/html', 'utf-8', '../app/interface/home.phtml');
    }
);

// GitHub.

App::match
(
    'GET', 'PUBLIC', 'github'

  , function()
    {
        App::redirect('301 Permanent Redirect', 'https://github.com/lucianolaranjeira/om');
    }
);