<?php
/**
 * ./om/dist/app/start.php
 *
 * @package    OM
 * @author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * @link       https://github.com/lucianolaranjeira/om
 * @version    Beta 2.5.3 • Sunday, February 10, 2019
 */

// Okay, Dorothy, let's get our hands dirty... so show me the yellow brick road!

require '../lib/Autoload.php';

use lib\Request;
use lib\Response;

// Application folder.

Request::$folder = '/projects/om/';

// Routes.

include '../app/routes/site.php';

// If a route wasn't found this is the right time to say something, isn't?

Response::status('308 Permanent Redirect');

Request::redirect('notfound');