<?php

namespace Expose;

class Report
{
    private $varName = null;
    private $varValue = null;
    private $filterMatches = array();

    public function __construct($varName = null, $varValue = null)
    {
        if ($varName !== null) {
            $this->setVarName($varName);    
        }
        if ($varValue !== null) {
            $this->setVarValue($varValue);
        }
    }

    public function setVarName($name)
    {
        $this->varName = $name;
    }

    public function getVarName()
    {
        return $this->varName;
    }

    public function setVarValue($value)
    {
        $this->varValue = $value;
    }

    public function getVarValue()
    {
        return $this->varValue;
    }

    public function addFilterMatch($match)
    {
        $match = (!is_array($match)) ? array($match) : $match;
        foreach ($match as $filter) {
            $this->filterMatches[] = $filter;    
        }
    }

    public function getFilterMatch()
    {
        return $this->filterMatches;
    }
}