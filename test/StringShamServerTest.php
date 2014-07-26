<?php

use pjdietz\ShamServer\StringShamServer;

class StringShamServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider statusCodeProvider
     */
    public function testMakeRequestAndReadStatusCode($statusCode)
    {
        $host = "localhost";
        $port = 8080;
        $router = "<?php http_response_code($statusCode);";

        // Start up a testing webserver.
        $server = new StringShamServer($host, $port, $router);

        // Make a request to the webserver.
        $ch = curl_init();
        $options = [
            CURLOPT_URL => "http://" . $host,
            CURLOPT_PORT => $port,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPGET => 1
        ];
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        $this->assertEquals($statusCode, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        curl_close($ch);

        // Shut down the webserver.
        $server->stop();
    }

    public function statusCodeProvider()
    {
        return [
            [200],
            [400],
            [401],
            [404]
        ];
    }

}
