<?php

namespace Expose\Export;

class Loopback extends \Expose\Export
{
    public function render()
    {
        return $this->getData();
    }
}
