<?php

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
     * Get the current set of pending records
     * 
     * @covers \Expose\Queue::pending
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