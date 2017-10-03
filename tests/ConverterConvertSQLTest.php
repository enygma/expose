<?php

namespace Expose\Converter;

class ConvertSQLTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test the issue #65
     *
     * @covers \Expose\Converter\ConvertSQL::convertFromSQLKeywords
     */
    public function testConvertFromSQLKeywordsWithNull()
    {
        $converter = new \Expose\Converter\ConvertSQL();
        $this->assertEquals($converter->convertFromSQLKeywords('SELECT 1,null;'), 'SELECT 1,0;');
        $this->assertEquals($converter->convertFromSQLKeywords('SELECT 1, null;'), 'SELECT 1,0;');
    }
}
