<?php

namespace Expose;

include_once 'MockMongoCollection.php';

class QueueTest extends \PHPUnit_Framework_TestCase
{
    private $queue = null;

    /**
     * Get a mock of the Queue object that returns the given results
     * 
     * @param mixed $return Return data
     * @return Mocked object
     */
    public function getQueueMock($return)
    {
        $collection = new \Expose\MockMongoCollection($return);

        $mock = $this->getMock('\\Expose\\Queue\\Mongo', array('getCollection'));
        $mock->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($collection));
        
        return $mock;
    }

    /**
     * Test the setting of the adapter on object construction
     * 
     * @covers \Expose\Queue::__construct
     * @covers \Expose\Queue::getAdapter
     */
    public function testSetAdapterOnConstruct()
    {
        $adapter = new \stdClass();
        $adapter->foo = 'test';
        $queue = new \Expose\Queue\Mongo($adapter);

        $this->assertEquals(
            $queue->getAdapter(),
            $adapter
        );
    }

    /**
     * Test the getter/setter for the adapter of the queue
     * 
     * @covers \Expose\Queue::getAdapter
     * @covers \Expose\Queue::setAdapter
     */
    public function testGetSetAdapter()
    {
        $adapter = new \stdClass();
        $adapter->foo = 'test';
        
        $queue = new \Expose\Queue\Mongo();
        $queue->setAdapter($adapter);

        $this->assertEquals(
            $queue->getAdapter(),
            $adapter
        );
    }

    /**
     * Get the current set of pending records
     * 
     * @covers \Expose\Queue::getPending
     */
    public function testGetPendingRecords()
    {
        $result = array(
            array(
                '_id' => '12345',
                'data' => array(
                    'POST' => array('test' => 'foo')
                ),
                'remote_ip' => '127.0.0.1',
                'datetime' => time(),
                'processed' => false
            )
        );

        $queue = $this->getQueueMock($result);
        $results = $queue->getPending();
        
        // be sure they're all "pending"
        $pass = true;
        foreach ($results as $result) {
            if ($result['processed'] !== false) {
                $pass = false;
            }
        }

        $this->assertTrue($pass, 'Non-pending records found');
    }
}