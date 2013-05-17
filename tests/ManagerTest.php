<?php

namespace Expose;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    private $manager = null;
    private $sampleFilters = array(
        array(
            "id" => "2",
            "rule" => "testmatch[0-9]+",
            "description" => "hard-coded match string",
            "tags" => array('tag' => array('test', 'sample')),
            "impact" => 2
        ),
    );

    public function setUp()
    {
        $filters = new \Expose\FilterCollection();
        $this->manager = new \Expose\Manager($filters);
    }

    public function executeFilters($data)
    {
        $filterCollection = new \Expose\FilterCollection();
        $filterCollection->setFilterData($this->sampleFilters);

        $manager = new \Expose\Manager($filterCollection);
        $manager->run($data);

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
}