<?php

namespace Expose;

class Manager
{
    private $data = null;
    private $filters = null;
    private $impact = 0;
    private $reports = array();

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
        $dataIterator = new \RecursiveIteratorIterator($data);
        foreach($dataIterator as $index => $value) {

            $filterMatches = array();
            foreach ($filters as $filter) {
                if ($filter->execute($value) === true) {
                    $filterMatches[] = $filter;
                    $this->impact += $filter->getImpact();
                }
            }

            if (!empty($filterMatches)) {
                $report = new \Expose\Report($index, $value);
                $report->addFilterMatch($filterMatches);
                $this->reports[] = $report;
            }
        }
    }

    public function getReports()
    {
        return $this->reports;
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