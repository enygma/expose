<?php

namespace Expose;

abstract class Queue
{
    /**
     * Connection adapter to use for queue requests
     * @var object
     */
    protected $adapter = null;

    /**
     * Init the queue and set the adapter if given
     *
     * @param object $adapter Connection adapter
     */
    public function __construct($adapter = null)
    {
        if ($adapter !== null) {
            $this->setAdapter($adapter);
        }
    }

    /**
     * Set the connection adapter for the request
     *
     * @param object $adapter Connection adapter
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Return the current connection adapter
     *
     * @return object Connection adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Get the current list of pending requests in the queue
     *
     * @param integer $limit Limit number of returned values
     * @return array Set of pending requests
     */
    public abstract function getPending($limit);

    /**
     * Add a new record to the queue
     *
     * @param array $data Request data
     */
    public abstract function add($data);

    /**
     * Mark the record processed (identified by ID)
     *
     * @param string $id Unique ID for record
     * @return boolean Success/fail of update
     */
    public abstract function markProcessed($id);
}
