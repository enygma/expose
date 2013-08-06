<?php

namespace Expose;

class MockLogger extends \Expose\Log
{
    public function __call($func, $args)
    {
        return true;
    }

    public function emergency($message, array $context = array())
    {
    	return true;
    }
	public function alert($message, array $context = array())
	{
		return true;
	}

	public function critical($message, array $context = array())
	{
		return true;
	}

	public function error($message, array $context = array())
	{
		return true;
	}

	public function warning($message, array $context = array())
	{
		return true;
	}

	public function notice($message, array $context = array())
	{
		return true;
	}

	public function info($message, array $context = array())
	{
		return true;
	}

	public function debug($message, array $context = array())
	{
		return true;
	}

	public function log($level, $message, array $context = array())
	{
		return true;
	}
}