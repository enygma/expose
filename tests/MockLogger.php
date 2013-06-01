<?php

namespace Expose;

class MockLogger
{
    public function __call($func, $args)
    {
        return true;
    }
}