<?php

namespace Expose;

class Queue
{
    /**
     * Queue database name
     * @var string
     */
    private $database = 'expose';

    /**
     * Queue database resource (collection)
     * @var string
     */
    private $resource = 'queue';

    /**
     * Init the object and set database and resource name optionally
     * 
     * @param string $databaseName Database name
     * @param string $resourceName Resource name
     */
    public function __construct($databaseName = null, $resourceName = null)
    {
        if ($resourceName !== null) {
            $this->setDatabase($databaseName);    
        }
        if ($databaseName !== null) {
            $this->setResource($resourceName);
        }
    }

    /**
     * Set the database name
     * 
     * @param string $databaseName Database name
     */
    public function setDatabase($databaseName)
    {
        $this->database = $databaseName;
    }

    /**
     * Get the current database name
     * 
     * @return string Database name
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Set the resource name
     * 
     * @param string $resourceName Resource name
     */
    public function setResource($resourceName)
    {
        $this->resource = $resourceName;
    }

    /**
     * Get the current resource name
     * 
     * @return string Resource name
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get the queue collection
     * 
     * @return \MongoCollection Collection instance
     */
    public function getCollection()
    {
        $queueDatabase = $this->getDatabase();
        $queueResource = $this->getResource();
        $db = new \MongoClient();

        return $db->$queueDatabase->$queueResource;
    }

    /**
     * Add a new record to the queue
     * 
     * @param array $requestData Request data
     */
    public function add($requestData)
    {
        $data = array(
            'data' => $requestData,
            'remote_ip' => (isset($_SERVER['REMOTE_ADDR'])) 
                ? $_SERVER['REMOTE_ADDR'] : 0,
            'datetime' => time(),
            'processed' => false
        );

        return $this->getCollection()->insert($data);
    }

    /**
     * Mark a record as processed
     * 
     * @param integer $id Record ID
     * @return boolean Success/fail of update
     */
    public function markProcessed($id)
    {
        return $this->getCollection()->update(
            array('_id' => $id),
            array('processed' => true)
        );
    }

    /**
     * Get the current list of pending records
     * 
     * @return array Record results
     */
    public function pending()
    {
        $results = $this->getCollection()
            ->find(array('processed' => false));
            
        return iterator_to_array($results);
    }
}