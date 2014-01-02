<?php

namespace Expose;

use Psr\Log\LoggerInterface;

abstract class Log implements LoggerInterface
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

    /**
     * Init the object and connect if string is given
     * 
     * @param object $connectString Connection string to logger instance
     */
	public function __construct($connectString = null)
	{
		if ($connectString !== null) {
			$this->connect($connectString);
		}
	}

    /**
     * Set the logger object instance
     *
     * @param object $logger Logger instance
     */
	public function setLogger($logger)
	{
		$this->logger = $logger;
	}

    /**
     * Get the current logger instance
     *
     * @return object Logger instance
     */
	public function getLogger()
	{
		return $this->logger;
	}
}
