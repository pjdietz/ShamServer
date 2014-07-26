<?php

namespace pjdietz\ShamServer;

class StringShamServer extends ShamServer
{
    /**
     * Create and start a new StringShamServer
     *
     * Pass an entire PHP file as a string for the $router parameter.
     *
     * @param string $host Hostname to listen for
     * @param int $port Port to list on
     * @param string $router PHP script as a string
     * @param int $timeout Time in microseconds to wait for the webserver to start
     * @internal param string $script Path to PHP router file
     */
    public function __construct($host, $port, $router, $timeout = 5000)
    {
        // Write the $router string to a temp file.
        $tempfile = tempnam(sys_get_temp_dir(), "php");
        file_put_contents($tempfile, $router);

        parent::__construct($host, $port, $tempfile, $timeout);
    }

    public function stop()
    {
        parent::stop();
        $script = $this->getScript();
        if (file_exists($script)) {
            unlink($script);
        }
    }
}
