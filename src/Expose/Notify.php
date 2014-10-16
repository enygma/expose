<?php

namespace Expose;

abstract class Notify
{
    /**
     * Send the notification
     *
     * @param array $filterMatches Current filter matches
     * @return boolean Success/fail
     */
    abstract function send($filterMatches);
}
