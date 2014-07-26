# Sham Server

Sham Server allows you to build mini Web servers for testing.

It uses  [PHP's built in Web server feature](http://php.net/manual/en/features.commandline.webserver.php) to spawn a separate process that listens for incoming requests and responds to them using the router script you provide.

```php
<?php

use pjdietz\ShamServer\ShamServer;

$host = "localhost";
$port = 8080;
$script = "/path/to/my/router.php";

// Start up a testing webserver.
$server = new ShamServer($host, $port, $script);

// Make a request to the webserver.
$ch = curl_init();
$options = [
    CURLOPT_URL => "http://" . $host,
    CURLOPT_PORT => $port,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_HTTPGET => 1
];
curl_setopt_array($ch, $options);
$result = curl_exec($ch);
curl_close($ch);

// Shut down the webserver.
$server->stop();
```

## StringShamRouter

You can also use `StringShamRouter` to create the router file for you. When you instantiate, pass a string containing the entirety of a PHP router script. The instance will write this to a temporary file, use it for the server, and them remove it.

```php
<?php
// Create a server that always respons with a 401 status code.
$host = "localhost";
$port = 8080;
$router = "<?php http_response_code(401);";
$server = new StringShamServer($host, $port, $router);
```

## Routers

For more information on how to write router scripts, see the [PHP Manual](http://php.net/manual/en/features.commandline.webserver.php).
