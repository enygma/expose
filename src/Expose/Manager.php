<?php

namespace Expose;

class Manager
{
    /**
     * Data to run the filter validation rules on
     * @var array
     */
    private $data = null;

    /**
     * Set of filters to execute
     * @var \Expose\FilterCollection
     */
    private $filters = null;

    /**
     * Overall impact score of the filter execution
     * @var integer
     */
    private $impact = 0;

    /**
     * Report results from the filter execution
     * @var array
     */
    private $reports = array();

    /**
     * Names of varaibles to ignore (exceptions to the rules)
     * @var array
     */
    private $exceptions = array();

    /**
     * Init the object and assign the filters
     * 
     * @param \Expose\FilterCollection $filters Set of filters
     */
    public function __construct(\Expose\FilterCollection $filters)
    {
        $this->setFilters($filters);
    }

    /**
     * Run the filters against the given data
     * 
     * @param array $data Data to run filters against
     */
    public function run(array $data)
    {
        $this->setData($data);
        $data = $this->getData();
        $filters = $this->getFilters();
        $impact = $this->impact;

        // run each of the filters on the data
        $dataIterator = new \RecursiveIteratorIterator($data);
        foreach($dataIterator as $index => $value) {

            // see if it's an exception
            if ($this->isException($index)) {
                continue;
            }

            $filterMatches = array();
            foreach ($filters as $filter) {
                if ($filter->execute($value) === true) {
                    $filterMatches[] = $filter;
                    $impact += $filter->getImpact();
                }
            }

            if (!empty($filterMatches)) {
                $report = new \Expose\Report($index, $value);
                $report->addFilterMatch($filterMatches);
                $this->reports[] = $report;
            }
        }
        $this->setImpact($impact);
    }

    /**
     * Get the current set of reports
     * 
     * @return array Set of \Expose\Reports
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * Get the current overall impact score
     * 
     * @return integer Impact score
     */
    public function getImpact()
    {
        return $this->impact;
    }

    /**
     * Set the overall impact value of the execution
     * 
     * @param integer $impact Impact value
     */
    public function setImpact($impact)
    {
        $this->impact = $impact;
    }

    /**
     * Set the source data for the execution
     * 
     * @param array $data Data to validate
     */
    public function setData(array $data)
    {
        $this->data = new \Expose\DataCollection($data);
    }

    /**
     * Get the current source data
     * 
     * @return array Source data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the filters for the current validation
     * 
     * @param \Expose\FilterCollection $filters Filter collection
     */
    public function setFilters(\Expose\FilterCollection $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Get the current set of filters
     * 
     * @return \Expose\FilterCollection Filter collection
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Add a variable name for an exception
     * 
     * @param string $varName Variable name
     */
    public function setException($varName)
    {
        $this->exceptions[] = $varName;
    }

    /**
     * Get a list of all exceptions
     * 
     * @return array Exception list
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * Test to see if a variable is an exception
     * 
     * @param string $varName Variable name
     * @return boolean Found/not found
     */
    public function isException($varName)
    {
        return in_array($varName, $this->exceptions);
    }

    /**
     * Expose the current set of reports in the given format
     * 
     * @param string $format Fromat for the export
     * @return mixed Report output (or null if the export type isn't found)
     */
    public function export($format = 'text')
    {
        $className = '\\Expose\\Export\\'.ucwords(strtolower($format));
        if (class_exists($className)) {
            $export = new $className($this->getReports());
            return $export->render();
        }
        return null;
    }
}