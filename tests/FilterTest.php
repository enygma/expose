<?php

namespace Expose;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    private $filter = null;

    public function setUp()
    {
        $this->filter = new \Expose\Filter();
    }

    /**
     * Test the getter/setter for impact value
     * 
     * @covers \Expose\Filter::setImpact
     * @covers \Expose\Filter::getImpact
     */
    public function testGetSetImpact()
    {
        $impact = 10;
        $this->filter->setImpact($impact);
        $this->assertEquals(
            $this->filter->getImpact(),
            $impact
        );
    }

    /**
     * Test the getter/setter for the tag value
     * 
     * @covers \Expose\Filter::setTags
     * @covers \Expose\Filter::getTags
     */
    public function testGetSetTags()
    {
        $tags = array('foo' => 'bar');
        $this->filter->setTags($tags);
        $this->assertEquals(
            $this->filter->getTags(),
            $tags
        );
    }

    /**
     * Test the getter/setter for the description
     * 
     * @covers \Expose\Filter::setDescription
     * @covers \Expose\Filter::getDescription
     */
    public function testGetSetDescription()
    {
        $desc = 'this is a description';
        $this->filter->setDescription($desc);
        $this->assertEquals(
            $this->filter->getDescription(),
            $desc
        );
    }

    /**
     * Test the getter/setter for the ID
     * 
     * @covers \Expose\Filter::setId
     * @covers \Expose\Filter::getId
     */
    public function testGetSetId()
    {
        $id = 12;
        $this->filter->setId($id);
        $this->assertEquals(
            $this->filter->getId(),
            $id
        );
    }

    /**
     * Test the gettet/setter for the regex rule
     * 
     * @covers \Expose\Filter::setRule
     * @covers \Expose\Filter::getRule
     */
    public function testGetSetRule()
    {
        $rule = '^foo[0-9]+';
        $this->filter->setRule($rule);
        $this->assertEquals(
            $this->filter->getRule(),
            $rule
        );
    }

    /**
     * Test that the result is true when a match is found
     *     for the rule
     * 
     * @covers \Expose\Filter::setRule
     * @covers \Expose\Filter::execute
     */
    public function testMatchDataValid()
    {
        $data = 'foo1234';
        $rule = '^foo[0-9]+';

        $this->filter->setRule($rule);
        $this->assertTrue(
            $this->filter->execute($data)
        );
    }

    /**
     * Test that the result is false when a match is not found
     * 
     * @covers \Expose\Filter::setRule
     * @covers \Expose\Filter::execute
     */
    public function testMAtchDataInvalid()
    {
        $data = 'barbaz';
        $rule = '^foo[0-9]+';

        $this->filter->setRule($rule);
        $this->assertFalse(
            $this->filter->execute($data)
        );
    }

    /**
     * Test that the output of the data in an array is correct
     * 
     * @covers \Expose\Filter::toArray
     */
    public function testOutputAsArray()
    {
        $data = array(
            'id' => 1234,
            'rule' => '^foo[0-9]+',
            'description' => 'this is a test',
            'tags' => array('csrf', 'xss'),
            'impact' => 3
        );
        $filter = new \Expose\Filter($data);
        $data['tags'] = 'csrf, xss';

        $this->assertEquals(
            $filter->toArray(), $data
        );
    }
}