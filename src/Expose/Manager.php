<?php

namespace Expose;

class Manager
{
    private $data = null;
    private $filters = null;
    private $impact = 0;

    public function __construct(\Expose\FilterCollection $filters)
    {
        $this->setFilters($filters);
    }

    public function run(array $data)
    {
        $this->setData($data);
        $data = $this->getData();
        $filters = $this->getFilters();

        // run each of the filters on the data
        foreach(new \RecursiveIteratorIterator($data) as $index => $value) {
            echo $index.' --> '; print_r($value);

            foreach ($filters as $filter) {
                if ($filter->execute($value) === true) {
                    $this->impact += $filter->getImpact();
                }
            }
        }
    }

    public function getImpact()
    {
        return $this->impact;
    }

    public function setData(array $data)
    {
        $this->data = new \Expose\DataCollection($data);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    public function getFilters()
    {
        return $this->filters;
    }
}