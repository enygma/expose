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

    /**
     * Test the issue #63
     *
     * @covers \Expose\Converter\ConvertSQL::convertFromSQLHex
     */
    public function testConvertFromSQLHex()
    {
        $converter = new \Expose\Converter\ConvertSQL();
        $this->assertEquals($converter->convertFromSQLHex('0x6D7973716C'), 'mysql');
        $this->assertEquals($converter->convertFromSQLHex('SELECT 0x6D7973716C'), 'SELECT mysql');
    }
}
