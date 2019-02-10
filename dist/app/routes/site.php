<?php
/**
 * ./om/dist/app/routes/site.php
 *
 * @package    OM
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.5.3 • Sunday, February 10, 2019
 */

use lib\File;
use lib\Request;
use lib\Response;
use lib\Route;

// Not Found (page). IMPORTANT! Never ever forget this route.

Route::match
(
    'notfound', Request::path()

  , function()
    {
        Response::status('404 Not Found');

        Response::content('text/html', 'utf-8');

        File::load('../app/interface/notfound.phtml');

        exit;
    }
);

// Index.

Route::match
(
    '', Request::path()

  , function()
    {
        Response::status('308 Permanent Redirect');

        Request::redirect('home');

        exit;
    }
);

// Home (page).

Route::match
(
    'home', Request::path()

  , function()
    {
        Response::status('200 OK');

        Response::content('text/html', 'utf-8');

        File::load('../app/interface/home.phtml');

        exit;
    }
);

// GitHub.

Route::match
(
    'github', Request::path()

  , function()
    {
        Response::status('308 Permanent Redirect');

        Request::redirect('https://github.com/lucianolaranjeira/om');

        exit;
    }
);