<?php

use pjdietz\ShamServer\ShamServer;

class ShamServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider scriptAndStatusCodeProvider
     */
    public function testMakeRequestAndReadStatusCode($statusCode, $routerScript)
    {
        // Start up a testing webserver.
        $host = "localhost";
        $port = 8080;
        $script = realpath(__DIR__ . "/routers/$routerScript");
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
        curl_exec($ch);
        $this->assertEquals($statusCode, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        curl_close($ch);

        // Shut down the webserver.
        $server->stop();
    }

    public function testStopOnTimeout()
    {
        // Start up a testing webserver.
        $host = "localhost";
        $port = 8080;
        $script = realpath(__DIR__ . "/routers/router-200.php");
        $this->setExpectedException('pjdietz\\ShamServer\\Exceptions\\TimeoutException');
        new ShamServer($host, $port, $script, 0);
    }

    public function testStopOnBadHost()
    {
        // Start up a testing webserver.
        $host = "nonlocalhost";
        $port = 80;
        $script = realpath(__DIR__ . "/routers/router-200.php");
        $this->setExpectedException('pjdietz\\ShamServer\\Exceptions\\CurlException');
        new ShamServer($host, $port, $script, 0);
    }

    public function testMissingRouterFile()
    {
        // Start up a testing webserver.
        $host = "localhost";
        $port = 8080;
        $script = realpath(__DIR__ . "/routers/nofile.php");
        $this->setExpectedException('pjdietz\\ShamServer\\Exceptions\\FileNotFoundException');
        new ShamServer($host, $port, $script, 0);
    }

    public function testCheckIfPidIsRunning()
    {
        // Start up a testing webserver.
        $host = "localhost";
        $port = 8080;
        $script = realpath(__DIR__ . "/routers/router-200.php");
        $server = new ShamServer($host, $port, $script);

        $pid = $server->getPid();
        $this->assertNotFalse(posix_getpgid($pid));
        $server->stop();
    }

    public function testCheckHost()
    {
        // Start up a testing webserver.
        $host = "localhost";
        $port = 8080;
        $script = realpath(__DIR__ . "/routers/router-200.php");
        $server = new ShamServer($host, $port, $script);
        $this->assertEquals($host, $server->getHost());
        $server->stop();
    }

    public function testCheckPort()
    {
        // Start up a testing webserver.
        $host = "localhost";
        $port = 8080;
        $script = realpath(__DIR__ . "/routers/router-200.php");
        $server = new ShamServer($host, $port, $script);
        $this->assertEquals($port, $server->getPort());
        $server->stop();
    }

    public function scriptAndStatusCodeProvider()
    {
        return [
            [200, "router-200.php"],
            [400, "router-400.php"],
            [404, "router-404.php"]
        ];
    }

}
