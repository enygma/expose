<?php

namespace Expose\Console\Command;

use PHPUnit\Framework\TestCase;

class FilterCommandTest extends TestCase
{
    public function testConstructor()
    {
        $tst = new FilterCommand("tst1");
        $this->assertInstanceOf('Expose\Console\Command\FilterCommand', $tst);
    }
}
