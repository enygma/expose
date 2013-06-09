<?php

namespace Expose;

include_once 'MockMongoCursor.php';

/**
 * Mock MongoCollection used for testing
 */
class MockMongoCollection
{
    private $data = null;

    public function __construct($data)
    {
        $this->data = new \Expose\MockMongoCursor($data);
    }

    public function __call($name, $args)
    {
        return $this->data;
    }
}