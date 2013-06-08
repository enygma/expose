<?php

namespace Expose;

/**
 * Mock MongoCursor used for testing
 */
class MockMongoCursor implements \Iterator
{
    private $data = array();
    private $position = 0;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __call($name, $args)
    {
        return $this;
    }

    public function current()
    {
        return $this->data[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return isset($this->data[$this->position]);
    }
}