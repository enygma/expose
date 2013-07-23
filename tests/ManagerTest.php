<?php

namespace Expose;

require_once 'MockLogger.php';
require_once 'MockQueue.php';

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    private $manager = null;
    private $sampleFilters = array(
        array(
            "id" => "2",
            "rule" => "testmatch[0-9]+",
            "description" => "hard-coded match string",
            "tags" => array('test', 'sample'),
            "impact" => 2
        ),
    );

    public function setUp()
    {
        $logger = new \stdClass();
        $filters = new \Expose\FilterCollection();
        $this->manager = new \Expose\Manager($filters, $logger);
    }

    public function executeFilters($data, $queue = false, $notify = false)
    {
        $filterCollection = new \Expose\FilterCollection();
        $filterCollection->setFilterData($this->sampleFilters);

        $logger = new MockLogger();
        $manager = new \Expose\Manager($filterCollection, $logger);
        $manager->setConfig(array('test' => 'foo'));
        $manager->run($data, $queue, $notify);

        return $manager;
    }

    /**
     * Test that the getter and setter for assigning filters 
     *     works correctly
     * 
     * @covers \Expose\Manager::getFilters
     * @covers \Expose\Manager::setFilters
     */
    public function testGetSetFilters()
    {
        $filters = new \Expose\FilterCollection();
        $filters->setFilterData($this->sampleFilters);

        $this->manager->setFilters($filters);

        $this->assertEquals(
            $filters,
            $this->manager->getFilters()
        );
    }

    /**
     * Test hte getter/setter for assigning data
     * 
     * @covers \Expose\Manager::getData
     * @covers \Expose\Manager::setData
     */
    public function testGetSetData()
    {
        $data = array('foo' => 'bar');

        $this->manager->setData($data);
        $getData = $this->manager->getData();
        $this->assertTrue(
            $getData instanceof \Expose\DataCollection
        );
    }

    /**
     * Test the getter/setter for the overall impact value
     * 
     * @covers \Expose\Manager::getImpact
     * @covers \Expose\Manager::setImpact
     */
    public function testGetSetImpact()
    {
        $impact = 12;
        $this->manager->setImpact($impact);
        $this->assertEquals(
            $impact,
            $this->manager->getImpact()
        );
    }

    /**
     * Test a successful (found) execution of the filters
     * 
     * @covers \Expose\Manager::run
     * @covers \Expose\Manager::getImpact
     * @covers \Expose\Manager::getReports
     */
    public function testRunSuccess()
    {
        $data = array(
            'POST' => array(
                'foo' => 'testmatch1'
            )
        );
        $manager = $this->executeFilters($data);

        $this->assertEquals($manager->getImpact(), 2);
        $this->assertEquals(count($manager->getReports()), 1);
    }

    /**
     * Test the use of the "export" method
     *     Loopback just returns the data back, no formatting
     * 
     * @covers \Expose\Manager::run
     * @covers \Expose\Manager::export
     */
    public function testRunExportFound()
    {
        $data = array(
            'POST' => array(
                'foo' => 'testmatch1'
            )
        );
        $manager = $this->executeFilters($data);

        $export = $manager->export('loopback');
        $this->assertEquals(count($export), 1);

        $report = array_shift($export);
        $this->assertTrue($report instanceof \Expose\Report);
    }

    /**
     * Test the null response when the export type isn't found
     * 
     * @covers \Expose\Manager::export
     */
    public function testRunExportNotFound()
    {
        $data = array(
            'POST' => array(
                'foo' => 'testmatch1'
            )
        );
        $manager = $this->executeFilters($data);

        $export = $manager->export('notvalid');
        $this->assertNull($export);
    }

    /**
     * Test the getter/setter for exceptions to processing
     * 
     * @covers \Expose\Manager::setException
     * @covers \Expose\Manager::isException
     * @covers \Expose\Manager::getExceptions
     */
    public function testGetSetException()
    {
        $this->manager->setException('testme');
        $this->assertTrue($this->manager->isException('testme'));

        $exceptions = $this->manager->getExceptions();
        $this->assertTrue(in_array('testme', $exceptions));
    }

    /**
     * Test the getter/setter for restrictions
     * 
     * @covers \Expose\Manager::setRestriction
     * @covers \Expose\Manager::getRestrictions
     */
    public function testGetSetRestriction()
    {
        $restriction = 'POST.bar.testing';
        $this->manager->setRestriction($restriction);
        $this->assertEquals(
            $this->manager->getRestrictions(),
            array($restriction)
        );
    }

    /**
     * Test the getter/setter for the log resource/table
     * 
     * @covers \Expose\Manager::setLogResource
     * @covers \Expose\Manager::getLogResource
     */
    public function testGetSetLogResource()
    {
        $resource = 'logs';
        $this->manager->setLogResource($resource);
        $this->assertEquals(
            $this->manager->getLogResource(),
            $resource
        );
    }

    /**
     * Test the getter/setter for the log database option
     * 
     * @covers \Expose\Manager::setLogDatabase
     * @covers \Expose\Manager::getLogDatabase
     */
    public function testGetSetLogDatabase()
    {
        $databaseName = 'expose';
        $this->manager->setLogDatabase($databaseName);
        $this->assertEquals(
            $this->manager->getLogDatabase(),
            $databaseName
        );
    }

    /**
     * Test the setup of the config based on an array (not a file)
     * 
     * @covers \Expose\Manager::setConfig
     * @covers \Expose\Manager::getConfig
     * @covers \Expose\Config::toArray
     */
    public function testSetupConfigArray()
    {
        $settings = array(
            'test' => 'foo'
        );
        $this->manager->setConfig($settings);
        $config = $this->manager->getConfig();
        $this->assertEquals(
            $config->toArray(),
            $settings
        );
    }

    /**
     * Test that a field marked as an exception is ignored
     * 
     * @covers \Expose\Manager::setException
     * @covers \Expose\Manager::run
     */
    public function testExceptionIsIgnored()
    {
        $filterCollection = new \Expose\FilterCollection();
        $filterCollection->setFilterData($this->sampleFilters);

        $logger = new MockLogger();
        $manager = new \Expose\Manager($filterCollection, $logger);
        $manager->setConfig(array('test' => 'foo'));
        $manager->setException('POST.foo');
        $manager->setException('POST.bar.baz');

        $data = array(
            'POST' => array(
                'foo' => 'testmatch1',
                'bar' => array(
                    'baz' => 'testmatch2'
                )
            )
        );        

        $manager->run($data);
        $this->assertEquals($manager->getImpact(), 0);
    }

    /**
     * Test that a field marked as an exception based on a regex wildcard is ignored
     * 
     * @covers \Expose\Manager::setException
     * @covers \Expose\Manager::run
     */
    public function testExceptionWildcardIsIgnored()
    {
        $filterCollection = new \Expose\FilterCollection();
        $filterCollection->setFilterData($this->sampleFilters);

        $logger = new MockLogger();
        $manager = new \Expose\Manager($filterCollection, $logger);
        $manager->setConfig(array('test' => 'foo'));
        $manager->setException('POST.foo[0-9]+');

        $data = array(
            'POST' => array(
                'foo1234' => 'testmatch1'
            )
        );        

        $manager->run($data);
        $this->assertEquals($manager->getImpact(), 0);
    }

    /**
     * Test the getter/setter for the Queue object
     * 
     * @covers \Expose\Manager::setQueue
     * @covers \Expose\Manager::getQueue
     */
    public function testGetSetQueue()
    {
        $queue = new \Expose\Queue\MockQueue();
        
        $this->manager->setQueue($queue);
        $this->assertEquals(
            $this->manager->getQueue(),
            $queue
        );
    }

    /**
     * Getting the default queue object without setting it
     *     first gives us the Mongo queue
     * 
     * @covers \Expose\Manager::getQueue
     */
    public function testGetDefaultQueue()
    {
        $queue = $this->manager->getQueue();
        $this->assertTrue(
            $queue instanceof \Expose\Queue\Mongo
        );
    }

    /**
     * Test the getter/setter for the notification method
     * 
     * @covers \Expose\Manager::getNotify
     * @covers \Expose\Manager::setNotify
     */
    public function testGetSetNotify()
    {
        $notify = new \Expose\Notify\Email();

        $this->manager->setNotify($notify);
        $this->assertEquals(
            $this->manager->getNotify(),
            $notify
        );
    }

}