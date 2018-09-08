<?php
/**
 * .\app\routes\site.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 1.0.3 • Friday, September 7, 2018
 */

use lib\App;

// Home.

App::match
(
    'PUBLIC', 'GET', ''

  , function()
    {
        App::redirect('301 Permanent Redirect', 'home');

        App::end();
    }
);

App::match
(
    'PUBLIC', 'GET', 'home'

  , function()
    {
        App::response('200 OK', 'text/html', 'utf-8', '../app/views/home.phtml');

        App::end();
    }
);

// App details.

App::match
(
    'PRIVATE', 'GET', 'details'

  , function()
    {
        App::response('200 OK', 'text/json', 'utf-8');

        echo App::details();

        App::end();
    }
);

// GitHub.

App::match
(
    'PUBLIC', 'GET', 'github'

  , function()
    {
        App::redirect('301 Permanent Redirect', 'https://github.com/lucianolaranjeira/om');

        App::end();
    }
);

// Just a secret.

App::match
(
    'BLOCKED', 'GET', 'secret'

  , 'Site::secret'
);

// Not Found.

App::match
(
    'PUBLIC', 'GET', 'notfound'

  , function()
    {
        App::response('200 OK', 'text/html', 'utf-8', '../app/views/notfound.phtml');

        App::end();
    }
);