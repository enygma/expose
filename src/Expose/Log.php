<?php

namespace Expose;

abstract class Log
{
	protected $logger = null;

	protected $resource = null;

	public abstract function emergency($message, array $context = array());
	public abstract function alert($message, array $context = array());
	public abstract function critical($message, array $context = array());
	public abstract function error($message, array $context = array());
	public abstract function warning($message, array $context = array());
	public abstract function notice($message, array $context = array());
	public abstract function info($message, array $context = array());
	public abstract function debug($message, array $context = array());
	public abstract function log($level, $message, array $context = array());

	public function __construct($connectString = null)
	{
		if ($connectString !== null) {
			$this->connect($connectString);
		}
	}

	public function setLogger($logger)
	{
		$this->logger = $logger;
	}
	public function getLogger()
	{
		return $this->logger;
	}
}