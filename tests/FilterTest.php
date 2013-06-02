<?php

namespace Expose;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    private $filter = null;

    public function setUp()
    {
        $this->filter = new \Expose\Filter();
    }

    public function testGetSetImpact()
    {
        $impact = 10;
        $this->filter->setImpact($impact);
        $this->assertEquals(
            $this->filter->getImpact(),
            $impact
        );
    }

    public function testGetSetTags()
    {
        $tags = array('foo' => 'bar');
        $this->filter->setTags($tags);
        $this->assertEquals(
            $this->filter->getTags(),
            $tags
        );
    }

    public function testGetSetDescription()
    {
        $desc = 'this is a description';
        $this->filter->setDescription($desc);
        $this->assertEquals(
            $this->filter->getDescription(),
            $desc
        );
    }
}