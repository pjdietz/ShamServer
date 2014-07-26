# ShamServer

ShamServer allows you to build mini web servers for testing.

It uses  [PHP's built in web server feature](http://php.net/manual/en/features.commandline.webserver.php) to spawn a separate process that listens for incoming requests and responds to them using the router script you provide.

```php
<?php

use pjdietz\ShamServer\ShamServer;

$host = "localhost";
$port = 8080;
$router = "/path/to/my/router.php";

// Start up a testing web server.
$server = new ShamServer($host, $port, $router);

// A server is now listening at http://localhost:8080

// Shut down the web server.
$server->stop();
```

## StringShamServer

You can also use `StringShamServer` to create the router file for you. When you instantiate, pass a string containing the entirety of a PHP router script. The instance will write this to a temporary file, use it for the server, and them remove it.

```php
<?php
// Create a server that always responds with a 401 status code.
$host = "localhost";
$port = 8080;
$router = "<?php http_response_code(401);";
$server = new StringShamServer($host, $port, $router);
```

## Routers

For more information on how to write router scripts, see the [PHP Manual](http://php.net/manual/en/features.commandline.webserver.php).


Copyright and License
---------------------
Copyright © 2014 by PJ Dietz
Licensed under the [MIT license](http://opensource.org/licenses/MIT)
