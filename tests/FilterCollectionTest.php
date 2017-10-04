<?php

namespace Expose;

class FilterConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Expose\FilterCollection
     */
    private $collection = null;

    public function setUp()
    {
        $this->collection = new FilterCollection();
    }

    /**
     * Test the getter/setter for the filter data in collection
     *
     * @covers \Expose\FilterCollection::getFilterData
     * @covers \Expose\FilterCollection::setFilterData
     */
    public function testGetSetFilterData()
    {
        $data = array(
            array('id' => 1234)
        );

        $filter = new \Expose\Filter();
        $filter->setId(1234);

        $this->collection->setFilterData($data);

        $result = $this->collection->getFilterData();
        $this->assertEquals($result[0], $filter);
    }

    /**
     * Test the getter/setter for the filter data in collection with an object
     *
     * @covers \Expose\FilterCollection::getFilterData
     * @covers \Expose\FilterCollection::setFilterData
     */
    public function testGetSetFilterDataWithObject()
    {
        $data = array(
            (object) array('id' => 1234)
        );
        $filter = new \Expose\Filter();
        $filter->setId(1234);

        $this->collection->setFilterData($data);

        $result = $this->collection->getFilterData();
        $this->assertEquals($result[0], $filter);
    }

    /**
     * Test the getter/setter for the filter data in collection with several filters
     *
     * @covers \Expose\FilterCollection::getFilterData
     * @covers \Expose\FilterCollection::setFilterData
     */
    public function testGetSetFilterDataWithMultipleFilters()
    {
        $filter1 = new \Expose\Filter(array('id' => 1234));
        $filter2 = new \Expose\Filter(array('id' => 5678));
        $data = array(
            array('id' => 1234),
            array('id' => 5678),
        );

        $this->collection->setFilterData($data);

        $result = $this->collection->getFilterData();
        $this->assertEquals($result[0], $filter1);
        $this->assertEquals($result[1], $filter2);
    }

    /**
     * Test the getter for the filter data in collection when requesting a single id
     *
     * @covers \Expose\FilterCollection::getFilterData
     * @covers \Expose\FilterCollection::setFilterData
     */
    public function testGetFilterDataWithId() {
        $data = array(
          array('id' => 1234)
        );

        $filter = new \Expose\Filter();
        $filter->setId(1234);

        $this->collection->setFilterData($data);

        $result = $this->collection->getFilterData(1234);
        $this->assertEquals($filter, $result);
    }

    /**
     * Tests setting the filter impact via the collection helper method
     *
     * @covers \Expose\FilterCollection::setFilterImpact
     */
    public function testGetSetFilterImpact() {
        $filter = new Filter();
        $filter->setId(1234);
        $filter->setImpact(3);

        $this->collection->addFilter($filter);

        $this->collection->setFilterImpact(1234, 27);
        $this->assertEquals(27, $filter->getImpact());
    }
}
