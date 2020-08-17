<?php

namespace Expose;

use PHPUnit\Framework\TestCase;

class ReportTest extends TestCase
{
    private $report = null;

    public function setUp(): void
    {
        $this->report = new Report();
    }

    /**
     * Test the getter/setter for the variable name
     *
     * @covers \Expose\Report::getVarName
     * @covers \Expose\Report::setVarName
     */
    public function testGetSetVariableName()
    {
        $name = 'var1';
        $this->report->setVarName($name);
        $this->assertEquals(
            $this->report->getVarName(),
            $name
        );
    }

    /**
     * Test the getter/setter for the variable value
     *
     * @covers \Expose\Report::setVarValue
     * @covers \Expose\Report::getVarValue
     */
    public function testGetSetVariableValue()
    {
        $value = 'value1';
        $this->report->setVarValue($value);
        $this->assertEquals(
            $this->report->getVarValue(),
            $value
        );
    }

    /**
     * Test the getter/setter for the variable path
     *
     * @covers \Expose\Report::setVarPath
     * @covers \Expose\Report::getVarPath
     */
    public function testGetSetVariablePath()
    {
        $value = 'value1';
        $this->report->setVarPath($value);
        $this->assertEquals(
            $this->report->getVarPath(),
            $value
        );
    }

    /**
     * Test the getter/setter for working with filter matches
     *
     * @covers \Expose\Report::addFilterMatch
     * @covers \Expose\Report::getfilterMatch
     */
    public function testGetSetFilterMatch()
    {
        $matches = array('match1', 'match2');
        $this->report->addFilterMatch($matches);
        $this->assertEquals(
            $matches,
            $this->report->getFilterMatch()
        );
    }

    /**
     * Convert the object into an array
     *
     * @covers \Expose\Report::toArray
     */
    public function testObjectToArray()
    {
        $this->report->setVarName('foo');
        $this->report->setVarValue('bar');

        $result = $this->report->toArray();
        $this->assertTrue(
            (isset($result['varName']) && $result['varName'] === 'foo')
            && (isset($result['varValue']) && $result['varValue'] === 'bar')
        );
    }

    /**
     * Test the "expansion" of filters (converting them to arrays too)
     *
     * @covers \Expose\Report::toArray
     */
    public function testObjectToArrayExpandFilters()
    {
        $filter = new Filter();
        $filter->setId(1234);

        $this->report->addFilterMatch($filter);
        $result = $this->report->toArray(true);

        $this->assertTrue(
            isset($result['filterMatches'][0]) && $result['filterMatches'][0]['id'] === 1234
        );
    }
}
