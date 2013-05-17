<?php

namespace Expose;

abstract class Export
{
    private $data = null;

    public function __construct($data)
    {
        $this->setData($data);
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public abstract function render();

}