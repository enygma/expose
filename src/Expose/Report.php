<?php

namespace Expose;

class Report
{
    /**
     * Name of variable being evaluated
     * @var string
     */
    private $varName = null;

    /**
     * Value of variable being evaluated
     * @var mixed
     */
    private $varValue = null;

    /**
     * Path of the variable being evaluated
     * @var mixed
     */
    private $varPath = null;

    /**
     * Set of filters matched for the report
     * @var array
     */
    private $filterMatches = array();

    /**
     * Init the object and optionally set the variable name/value
     *
     * @param string $varName Variable name [optional]
     * @param mixed $varValue Variable value [optional]
     */
    public function __construct($varName = null, $varValue = null, $varPath = null)
    {
        if ($varName !== null) {
            $this->setVarName($varName);
        }
        if ($varValue !== null) {
            $this->setVarValue($varValue);
        }
        if ($varPath !== null) {
            $this->setVarPath($varPath);
        }
    }

    /**
     * Set the current variable's name
     *
     * @param string $name Variable name
     */
    public function setVarName($name)
    {
        $this->varName = $name;
    }

    /**
     * Get the current variable's name
     *
     * @return string Variable name
     */
    public function getVarName()
    {
        return $this->varName;
    }

    /**
     * Set variable value
     *
     * @param mixed $value Variable value
     */
    public function setVarValue($value)
    {
        $this->varValue = $value;
    }

    /**
     * Get varaible value
     *
     * @return mixed variable value
     */
    public function getVarValue()
    {
        return $this->varValue;
    }

    /**
     * Set variable path in data array
     *
     * @param mixed $path Variable path
     */
    public function setVarPath($path)
    {
        $this->varPath = $path;
    }

    /**
     * Gets the variable path in data array
     *
     * @return mixed variable path
     */
    public function getVarPath()
    {
        return $this->varPath;
    }

    /**
     * Add filter match(es) for the report/variable relation
     *     Can take in either a single filter or a set
     *
     * @param mixed $match Either a single \Expose\Filter or an array of them
     */
    public function addFilterMatch($match)
    {
        $match = (!is_array($match)) ? array($match) : $match;
        foreach ($match as $filter) {
            $this->filterMatches[] = $filter;
        }
    }

    /**
     * Get the current set of filter matches
     *
     * @return array Filter match set
     */
    public function getFilterMatch()
    {
        return $this->filterMatches;
    }

    /**
     * Convert the object to an array
     *
     * @return array Filter data
     */
    public function toArray($expandFilters = false)
    {
        $matches = $this->getFilterMatch();
        if ($expandFilters === true) {
            foreach ($matches as $index => $match) {
                $matches[$index] = $match->toArray();
            }
        }

        return array(
            'varName' => $this->getVarName(),
            'varValue' => $this->getVarValue(),
            'varPath' => $this->getVarPath(),
            'filterMatches' => $matches
        );
    }
}