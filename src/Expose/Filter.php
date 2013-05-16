<?php

namespace Expose;

class Filter
{
    private $id = null;
    private $rule = null;
    private $description = null;
    private $tags = array();
    private $impact = 0;

    public function __construct(array $data = null)
    {
        if ($data !== null) {
            $this->load($data);
        }
    }

    public function load($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        foreach ($data as $index => $value) {
            $this->$index = $value;
        }
    }

    public function getImpact()
    {
        return $this->impact;
    }

    public function execute($data)
    {
        echo $this->description."\n";
        return (preg_match('/'.$this->rule.'/', $data) === 1) ? true : false;
    }
}