<?php

namespace pjdietz\ShamServer;

class StringShamServer extends ShamServer
{
    public function __construct($host, $port, $router, $timeout = 5000, $sleepInterval = 100000000)
    {
        // Write the $router string to a temp file.
        $tempfile = tempnam(sys_get_temp_dir(), "php");
        file_put_contents($tempfile, $router);

        parent::__construct($host, $port, $tempfile, $timeout, $sleepInterval);
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
