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
            if ($index == 'tags' && !is_array($value)) {
                // normalize to an array
                $value->tag = (!is_array($value->tag)) ? array($value->tag) : $value->tag;
            }
            $this->$index = $value;
        }
    }

    public function getImpact()
    {
        return $this->impact;
    }

    public function getTags()
    {
        return $this->tags->tag;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getId()
    {
        return $this->id;
    }

    public function execute($data)
    {
        return (preg_match('/'.$this->rule.'/', $data) === 1) ? true : false;
    }
}