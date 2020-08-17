<?php

namespace Expose\Queue;

use MongoDB\Driver\Manager;
use PHPUnit\Framework\TestCase;

class MongoTest extends TestCase
{

    /**
     * @var Mongo
     */
    private $test;

    protected function setUp()
    {
        $this->test = new Mongo();
    }

    public function testGetAdapter()
    {
        $this->assertInstanceOf(Manager::class, $this->test->getAdapter());
    }
}
