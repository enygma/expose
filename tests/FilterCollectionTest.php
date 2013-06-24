<?php

namespace Expose;

class FilterConnectionTest extends \PHPUnit_Framework_TestCase
{
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

}