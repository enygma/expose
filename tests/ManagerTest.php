<?php

namespace Expose;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    private $manager = null;
    private $sampleFilters = array(
        array(
            "id" => "1",
            "rule" => "(?:\"[^\"]*[^-]?>)|(?:[^\\w\\s]\\s*\\\/>)|(?:>\")",
            "description" => "finds html breaking injections including whitespace attacks",
            "tags" => array('tag' => array('xss', 'csrf')),
            "impact" => 4
        )
    );

    public function setUp()
    {
        $filters = new \Expose\FilterCollection();
        $this->manager = new \Expose\Manager($filters);
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
}