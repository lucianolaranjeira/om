<?php
/**
 * .\app\routes\site.php
 *
 * @package    Om
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 1.0.0 • Tuesday, August 21, 2018
 */

use lib\App;

// Home.

App::match('GET', '', 'Site::index');

App::match('GET', 'home', 'Site::home');

// Not Found.

App::match('GET', 'notfound', 'Site::notfound');

// GitHub.

App::match('GET', 'github', 'Site::github');