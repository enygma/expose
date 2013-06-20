<?php

namespace Expose;

abstract class Notify
{
    private $config = array();

    public function __construct($config)
    {
        $this->setConfig($config);
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    abstract function send($filterMatches);
}