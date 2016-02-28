<?php

namespace Expose;

class FilterCollection implements \ArrayAccess, \Iterator, \Countable
{
    private $filterPath = 'filter_rules.json';
    private $filterData = array();
    private $index = 0;
    private $cache = null;
    
    public function __construct( \Expose\Cache $cache = null) {
        if (!is_null($cache) ) {
            $this->setCache($cache);
        }
    }
    /**
     * Set the cache object
     *
     * @param ExposeCache $cache Cache instance
     */
    public function setCache(\Expose\Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get the current cache instance
     *
     * @return mixed Either a \Expose\Cache instance or null
     */
    public function getCache()
    {
        return $this->cache;
    }
    
    public function rewind()
    {
        $this->index = 0;
    }
    public function current()
    {
        return $this->filterData[$this->index];
    }

    public function key()
    {
        return $this->index;
    }

    public function next()
    {
        $this->index++;
    }

    public function valid()
    {
        return (isset($this->filterData[$this->index]));
    }

    public function count()
    {
        return count($this->filterData);
    }

    public function offsetGet($offset)
    {
        return (isset($this->filterData[$offset]))
            ? $this->filterData[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->filterData[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->filterData[$offset]);
    }

    public function offsetUnset($offset)
    {
        if (isset($this->filterData[$offset])) {
            unset($this->filterData[$offset]);
        }
    }

    public function load($path = null)
    {
        $sig = md5(print_r('filters', true));
        $cache = $this->getCache();
        if ($cache !== null) {
            $cacheData = $cache->get($sig);
            if ($cacheData !== null) {
                $this->setFilterData($cacheData);
            }
        }
        
        $loadFile = __DIR__.'/'.$this->filterPath;
        if ($path !== null && is_file($path)) {
            $loadFile = $path;
        }
        $data = json_decode(file_get_contents($loadFile));
        if ($cache !== null) {
            $cache->save($sig, $data->filters);
        }
        $this->setFilterData($data->filters);
        
        
    }

    /**
     * Set the current filter data
     *
     * @param array $data Filter data
     */
    public function setFilterData($data)
    {
        foreach ($data as $index => $config) {
            if (is_object($config)) {
                $config = get_object_vars($config);
            }
            $filter = new \Expose\Filter($config);
            $this->addFilter($filter);
        }
    }

    /**
     * Return all current filter data (or one specific filter)
     *
     * @param integer $filterId Filter ID #
     * @return mixed Either array of all filters or object of single filter
     */
    public function getFilterData($filterId = null)
    {
        if ($filterId !== null) {
            foreach ($this->filterData->filters->filter as $filter) {
                if ($filter->id == $filterId) {
                    return $filter;
                }
            }
            return null;
        } else {
            return $this->filterData;
        }
    }

    /**
     * @param string $path Location of json filter set
     */
    public function setFilterPath($path)
    {
        $this->filterPath=$path;
    }

    /**
     * Add a new Filter object to the set
     *
     * @param \Expose\Filter $filter Filter object
     */
    public function addFilter(\Expose\Filter $filter)
    {
        $this->filterData[] = $filter;
    }
}
