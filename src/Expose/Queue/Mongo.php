<?php

namespace Expose\Queue;

class Mongo implements \Expose\Queue
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
    private $collection = 'queue';

    /**
     * Get the queue collection
     * 
     * @return \MongoCollection Collection instance
     */
    public function getCollection()
    {
        $queueDatabase = $this->database;
        $queueResource = $this->collection;
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
            array('$set' => array('processed' => true))
        );
    }

    /**
     * Get the current list of pending records
     * 
     * @return array Record results
     */
    public function getPending($limit = 10)
    {
        $results = $this->getCollection()
            ->find(array('processed' => false))
            ->limit($limit);

        return iterator_to_array($results);
    }
}