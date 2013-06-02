<?php

namespace Expose;

class ReportTest extends \PHPUnit_Framework_TestCase
{
    private $report = null;

    public function setUp()
    {
        $this->report = new \Expose\Report();
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
}
