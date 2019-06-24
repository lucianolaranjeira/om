<?php
/**
 * app/routes/site.php (file)
 *
 * @package OM
 * @author  Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link    https://github.com/lucianolaranjeira/om
 * @version Beta 2.7.0 • Monday, June 24, 2019
 */

use lib\File;
use lib\Request;
use lib\Response;
use lib\Route;

// Request::$folder = '/projects/om/'; // Set your project folder (optional).

// Index (redirect).

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

// Dump (just get REQUEST info).

Route::match
(
    'dump', Request::path()

  , function()
    {
        Response::status('200 OK');

        Response::content('text/html', 'utf-8');

        echo '<pre>';

        Request::dump();

        echo '</pre>';

        exit;
    }
);

// GitHub (redirect).

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

// Not Found (page).

// IMPORTANT! Never ever forget this route.

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

// If a route wasn't found this is the right time to say something, isn't?

Response::status('308 Permanent Redirect');

Request::redirect('notfound');