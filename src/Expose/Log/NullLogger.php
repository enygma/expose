<?php

namespace Expose\Log;

class NullLogger extends \Psr\Log\AbstractLogger
{
    /**
     * Don't push the log message
     *
     * @param string $level Logging level (ex. info, debug, notice...)
     * @param string $message Log message
     * @param array $context Extra context information
     * @return boolean Always true
     */
    public function log($level, $message, array $context = array())
    {
        return true;
    }
}
