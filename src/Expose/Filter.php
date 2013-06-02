<?php

namespace Expose;

class Filter
{
    /**
     * Filter ID
     * @var integer
     */
    private $id = null;

    /**
     * Filter Regex match rule
     * @var string
     */
    private $rule = null;

    /**
     * Filter description
     * @var string
     */
    private $description = null;

    /**
     * Filter tag set
     * @var array
     */
    private $tags = array();

    /**
     * Filter impact rating
     * @var integer
     */
    private $impact = 0;

    /**
     * Init the filter and set the data if given
     * 
     * @param array $data Filter data [optional]
     */
    public function __construct(array $data = null)
    {
        if ($data !== null) {
            $this->load($data);
        }
    }

    /**
     * Load the data into the filter object
     * 
     * @param array $data Filter data
     */
    public function load($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        foreach ($data as $index => $value) {
            if ($index == 'tags' && !is_array($value)) {
                if (isset($value->tag)) {
                    $value = (!is_array($value->tag)) ? array($value->tag) : $value->tag;
                }
            }
            $this->$index = $value;
        }
    }

    /**
     * Get the current filter's impact
     * 
     * @return integer Impact level
     */
    public function getImpact()
    {
        return $this->impact;
    }

    /**
     * Set the impact level of the filter
     * 
     * @param integer $impact Impact rating (Ex. "8" or "3")
     * @return \Expose\Filter instance
     */
    public function setImpact($impact)
    {
        $this->impact = $impact;
        return $this;
    }

    /**
     * Get the current list of tags for the filter
     * 
     * @return array Set of tags
     */
    public function getTags()
    {
        return (isset($this->tags)) ? $this->tags : array();
    }

    /**
     * Set the tags that match on the filter
     * 
     * @param array $tags Tag set
     * @return \Expose\Filter instance
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the current Filter's description
     * 
     * @param string $desc Description of filter
     * @return \Expose\Filter instance
     */
    public function setDescription($desc)
    {
        $this->description = $desc;
        return $this;
    }

    /**
     * Get the current filter's ID
     * 
     * @return integer Filter ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the current filter's ID
     * 
     * @param integer $id Filter ID
     * @return \Expose\Filter instance
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the current Rule value
     * 
     * @return string Regex rule
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Set the current regex match rule
     * 
     * @param string $rule Regex match string
     * @return \Expose\Filter instance
     */
    public function setRule($rule)
    {
        $this->rule = $rule;
        return $this;
    }

    public function execute($data)
    {
        return (preg_match('/'.$this->rule.'/', $data) === 1) ? true : false;
    }

    /**
     * Return the current Filter's data as an array
     * 
     * @return array Filter data
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'rule' => $this->getRule(),
            'description' => $this->getDescription(),
            'tags' => implode(', ', $this->getTags()),
            'impact' => $this->getImpact()
        );
    }
}