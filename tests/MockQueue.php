<?php

namespace Expose\Queue;

class MockQueue extends \Expose\Queue
{
    public function getPending($limit)
    {
        return array();
    }

    public function markProcessed($id)
    {
        return true;
    }

    public function add($data)
    {
        return true;
    }
}