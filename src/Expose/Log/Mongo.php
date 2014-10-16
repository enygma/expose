<?php

namespace Expose\Log;

class Mongo extends \Expose\Log
{
    /**
     * Database collection
     * @var string
     */
    private $dbCollection = 'logs';

    /**
     * Database name
     * @var string
     */
    private $dbName = 'expose';

    /**
     * Mongo connection string
     * @var string
     */
    private $connectString = null;

    /**
     * Init the object and set the connection string if given
     *
     * @param string $connectString Mongo connection string
     */
    public function __construct($connectString = null)
    {
        if ($connectString !== null) {
            $this->setConnectString($connectString);
        }
    }

    /**
     * Get the current database name
     *
     * @return string DB name
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * Set the current database name
     *
     * @param string $name DB name
     */
    public function setDbName($name)
    {
        $this->dbName = $name;
    }

    /**
     * Get the current DB collection name
     *
     * @return string Collection name
     */
    public function getDbCollection()
    {
        return $this->dbCollection;
    }

    /**
     * Set the current DB collection name
     *
     * @param string $collection Collection name
     */
    public function setDbCollection($collection)
    {
        $this->dbCollection = $collection;
    }

    /**
     * Set the current connection string
     *
     * @param string $string Connection string
     */
    public function setConnectString($string)
    {
        $this->connectString = $string;
    }

    /**
     * Get the current connection string
     *
     * @return string Connection string
     */
    public function getConnectString()
    {
        return $this->connectString;
    }

    /**
     * Log emergency messages
     *
     * @param string $message Log message
     * @param array $context Extra contact information
     * @return boolean Log pass/fail
     */
    public function emergency($message, array $context = array())
    {
        return $this->log('emergency', $message, $context);
    }

    /**
     * Log alert messages
     *
     * @param string $message Log message
     * @param array $context Extra contact information
     * @return boolean Log pass/fail
     */
    public function alert($message, array $context = array())
    {
        return $this->log('alert', $message, $context);
    }

    /**
     * Log critical messages
     *
     * @param string $message Log message
     * @param array $context Extra contact information
     * @return boolean Log pass/fail
     */
    public function critical($message, array $context = array())
    {
        return $this->log('critical', $message, $context);
    }

    /**
     * Log error messages
     *
     * @param string $message Log message
     * @param array $context Extra contact information
     * @return boolean Log pass/fail
     */
    public function error($message, array $context = array())
    {
        return $this->log('error', $message, $context);
    }

    /**
     * Log warning messages
     *
     * @param string $message Log message
     * @param array $context Extra contact information
     * @return boolean Log pass/fail
     */
    public function warning($message, array $context = array())
    {
        return $this->log('warning', $message, $context);
    }

    /**
     * Log notice messages
     *
     * @param string $message Log message
     * @param array $context Extra contact information
     * @return boolean Log pass/fail
     */
    public function notice($message, array $context = array())
    {
        return $this->log('notice', $message, $context);
    }

    /**
     * Log info messages
     *
     * @param string $message Log message
     * @param array $context Extra contact information
     * @return boolean Log pass/fail
     */
    public function info($message, array $context = array())
    {
        return $this->log('info', $message, $context);
    }

    /**
     * Log debug messages
     *
     * @param string $message Log message
     * @param array $context Extra contact information
     * @return boolean Log pass/fail
     */
    public function debug($message, array $context = array())
    {
        return $this->log('debug', message, context);
    }

    /**
     * Push the log message and context information into Mongo
     *
     * @param string $level Logging level (ex. info, debug, notice...)
     * @param string $message Log message
     * @param array $context Extra context information
     * @return boolean Success/fail of logging
     */
    public function log($level, $message, array $context = array())
    {
        $logger = new \Monolog\Logger('audit');
        try {
            $handler = new \Monolog\Handler\MongoDBHandler(
                new \Mongo($this->getConnectString()),
                $this->getDbName(),
                $this->getDbCollection()
            );
        } catch (\MongoConnectionException $e) {
            throw new \Exception('Cannot connect to Mongo - please check your server');
        }
        $logger->pushHandler($handler);
        $logger->pushProcessor(function ($record) {
            $record['datetime'] = $record['datetime']->format('U');
            return $record;
        });

        return $logger->$level($message, $context);
    }
}
