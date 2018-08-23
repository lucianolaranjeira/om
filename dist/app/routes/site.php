<?php
/**
 * .\app\routes\site.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 1.0.2 • Tuesday, August 22, 2018
 */

use lib\App;

// Home.

App::match('GET', '', 'Site::index');

App::match('GET', 'home', 'Site::home');

// App details.

App::match('GET', 'details', 'Site::details');

// GitHub.

App::match('GET', 'github', 'Site::github');

// Not Found.

App::match('GET', 'notfound', 'Site::notfound');