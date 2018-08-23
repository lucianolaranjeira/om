<?php
/**
 * .\app\controllers\Site.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 1.0.2 • Tuesday, August 22, 2018
 */

namespace app\controllers;

use lib\App;

abstract class Site
{
    /**
     * Get home page.
     *
     * @return void
     */
    public static function index()
    {
        App::status('301 Permanent Redirect');

        App::bypass('home');

        App::end();
    }

    /**
     * Get home page.
     *
     * @return void
     */
    public static function home()
    {
        App::status('200 OK');

        App::content('text/html', 'utf-8');

        App::load('../app/views/home.phtml');

        App::end();
    }

    /**
     * Get app details.
     *
     * @return void
     */
    public static function details()
    {
        App::status('200 OK');

        App::content('text/json', 'utf-8');

        echo App::details();

        App::end();
    }

    /**
     * Go to GitHub.
     *
     * @return void
     */
    public static function github()
    {
        App::status('301 Permanent Redirect');

        App::redirect('https://github.com/lucianolaranjeira/om');

        App::end();
    }

    /**
     * Get not found page.
     *
     * @return void
     */
    public static function notfound()
    {
        App::status('404 Not Found');

        App::content('text/html', 'utf-8');

        App::load('../app/views/notfound.phtml');

        App::end();
    }
}