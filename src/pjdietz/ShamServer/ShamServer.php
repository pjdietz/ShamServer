<?php

namespace pjdietz\ShamServer;

use pjdietz\ShamServer\Exceptions\CurlException;
use pjdietz\ShamServer\Exceptions\FileNotFoundException;
use pjdietz\ShamServer\Exceptions\TimeoutException;

/**
 * Class for creating a PHP-based webserver for testing purposes.
 *
 * The class starts an additional PHP process running a command-line webserver
 * http://php.net/manual/en/features.commandline.webserver.php
 */
class ShamServer
{
    const SLEEP_INTERVAL = 100000000;

    /** @var int Process Identifier for the webserver */
    private $pid;
    /** @var string Hostname the webserver listens for */
    private $host;
    /** @var int HTTP port the webserver listens on */
    private $port;
    /** @var string Path to the PHP script the webserver will serve */
    private $script;
    /** @var int Time in microseconds to wait for the webserver to start */
    private $timeout;
    /** @var array Associative array of cURL options */
    private $options;

    /**
     * Create and start a new ShamServer
     *
     * @param  string                           $host    Hostname to listen for
     * @param  int                              $port    Port to list on
     * @param  string                           $script  Path to PHP router file
     * @param  int                              $timeout Time in microseconds to wait for the webserver to start
     * @throws Exceptions\FileNotFoundException
     * @throws Exceptions\TimeoutException
     */
    public function __construct($host, $port, $script, $timeout = 5000)
    {
        if (!file_exists($script)) {
            throw new FileNotFoundException("Unable to file file $script");
        }

        $this->host = $host;
        $this->port = $port;
        $this->script = $script;
        $this->timeout = $timeout;
        $this->options = [
            CURLOPT_URL => "http://" . $host,
            CURLOPT_PORT => $port,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPGET => 1
        ];
        $this->start();
    }

    public function __destruct()
    {
        $this->stop();
    }

    public function start()
    {
        if (!isset($this->pid)) {
            $command = "php -S $this->host:$this->port $this->script  > /dev/null 2>&1 & echo $!; ";
            $output = array();
            exec($command, $output);
            $this->pid = (int) $output[0];
            $this->wait();
        }
    }

    public function stop()
    {
        if (isset($this->pid)) {
            $command = "kill $this->pid";
            exec($command);
        }
        unset($this->pid);
    }

    /**
     * Return true if able to make a cURL request to the server.
     *
     * @return bool
     * @throws Exceptions\CurlException
     */
    public function isWebserverListening()
    {
        $ch = curl_init();
        curl_setopt_array($ch, $this->options);
        $result = curl_exec($ch);
        if ($result === false) {
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            if ($errno !== 7) {
                throw new CurlException($error, $errno);
            }
        }
        curl_close($ch);

        return $result !== false;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Block while while the server is starting up.
     *
     * @throws Exceptions\TimeoutException
     */
    private function wait()
    {
        $start = microtime();
        while (!$this->isWebserverListening()) {
            // Check for timeout.
            $current = microtime();
            if ($current >= $start + $this->timeout) {
                // Kill the webserver process and throw an exception.
                $this->stop();
                throw new TimeoutException();
            }
            // Sleep, then try again.
            time_nanosleep(0, self::SLEEP_INTERVAL);
        }
    }
}
