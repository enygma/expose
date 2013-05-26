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
        $impact = $this->impact;

        $path = array();
        $filterMatches = $this->runFilters($data, $path);
    }

    /**
     * Run through the filters on the given data
     * 
     * @param array $data Data to check
     * @param array $path Current "path" in the data
     * @param integer $lvl Current nesting level
     * @return array Set of filter matches
     */
    public function runFilters($data, $path, $lvl = 0)
    {
        $filterMatches = array();

        foreach ($data as $index => $value) {
            if (count($path) > $lvl) {
                $path = array_slice($path, 0, $lvl);
            }
            $path[] = $index;

            // see if it's an exception
            if ($this->isException(implode('.', $path))) {
                continue;
            }

            if (is_array($value)) {
                $lvl++;
                $filterMatches = array_merge(
                    $filterMatches,
                    $this->runFilters($value, $path, $lvl)
                );
            } else {
                foreach ($this->getFilters() as $filter) {
                    if ($filter->execute($value) === true) {
                        $filterMatches[] = $filter;

                        $report = new \Expose\Report($index, $value);
                        $report->addFilterMatch($filter);
                        $this->reports[] = $report;

                        $this->impact += $filter->getImpact();
                    }
                }
            }
        }
        return $filterMatches;
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
    public function setException($path)
    {
        $path = (!is_array($apth)) ? array($path) : $path;
        $this->exceptions[] = array_merge($this->exceptions, $path);
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
     *     Checks can be exceptions, so we preg_match it
     * 
     * @param string $path Variable "path" (Ex. "POST.foo.bar")
     * @return boolean Found/not found
     */
    public function isException($path)
    {
        $isException = false;
        foreach ($this->exceptions as $exceptions) {
            $ex = str_replace('.', '\\.', $exceptions);
            if ($isException === false && preg_match('/^'.$ex.'$/', $path) !== 0) {
                $isException = true;
            }
        }

        return $isException;
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