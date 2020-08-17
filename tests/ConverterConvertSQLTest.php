<?php

namespace Expose\Converter;

use PHPUnit\Framework\TestCase;

class ConvertSQLTest extends TestCase
{

    /**
     * Test the issue #65
     *
     * @covers \Expose\Converter\ConvertSQL::convertFromSQLKeywords
     */
    public function testConvertFromSQLKeywordsWithNull()
    {
        $converter = new ConvertSQL();
        $this->assertEquals($converter->convertFromSQLKeywords('SELECT 1,null;'), 'SELECT 1,0;');
        $this->assertEquals($converter->convertFromSQLKeywords('SELECT 1, null;'), 'SELECT 1,0;');
    }
}
