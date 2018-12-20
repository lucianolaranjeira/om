<p align="center">
    <img height="250" width="250" src="https://github.com/lucianolaranjeira/om/blob/master/design/om.png">
</p>

# OM
OM is a micro PHP router for web development.

Sometimes you want to built really small PHP websites and/or webservices (API) using friendly URLs such as "myscompany.com/services" or "mycompany.com/contact" without using frameworks like Laravel because you actually don't need a bunch of features or functionalities.

Examples:

```php

// Example 1

if (Route::match('index', Request::path()))
{
    // Do something...
}

// Example 2.

if (Request::method() === 'POST')
{
    Route::match
    (
        'email', Request::path()

      , function()
        {
            $parameters = Request::parameters();

            if (key_exists('email', $parameters))
            {
                $email = $parameters['email'];
            }

            Response::status('200 OK');
            
            exit;
        }
    );
}

// Example 3.

if (Request::method() === 'GET')
{
    Route::match
    (
        'details/{order_id}', Request::path()

      , function($order_id)
        {
            Response::status('200 OK');

            Response::content('application/json', 'utf-8');

            echo json_encode(array('order_id' => $order_id));

            exit;
        }
    );
}


// Example 4.

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


```

So here are some files to help you to do this small work.

Your website will be inside the "dist" folder. To use:

1 - Download this files;

2 - Create your own routes files on "dist\app\routes\" folder;

3 - Edit "dist\app\start.php" file;

4 - Add your own code on "dist\app\" folder;

5 - Publish what you have inside the "dist" folder.

For more information, get in touch.

# Gulp
To help you to build assets for your OM website, use the gulp folder (if you don't wanna use it, just delete these folder):

1 - Install the node modules (node.js is required):

```
npm install

```

2 - List the gulp tasks:

```
gulp

```

Help yourself!