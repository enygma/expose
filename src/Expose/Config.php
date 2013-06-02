<?php

namespace Expose;

class Config
{
    /**
     * Configuration data
     * @var array
     */
    private $config = array();

    /**
     * Init the object and set data if given
     * 
     * @param array $data Configuration data
     */
    public function __construct(array $data = null)
    {
        if ($data !== null) {
            $this->load($data);
        }
    }

    /**
     * Load the data into the object
     * 
     * @param array $data Configuration data
     */
    public function load(array $data)
    {
        foreach ($data as $index => $value) {
            $this->set($index, $value);
        }
    }

    /**
     * Get the value from the config by "path"
     * 
     * @param string $path Path to config option (Ex. "foo.bar.baz")
     * @return mixed Either the found value or null if not found
     */
    public function get($path)
    {
        $p = explode('.', $path);
        $cfg = &$this->config;
        $count = 1;

        foreach ($p as $part) {
            if (array_key_exists($part, $cfg)) {
                // see if it's the end
                if ($count == count($p)) {
                    echo 'end';
                    return $cfg[$part];
                }
                $cfg = &$cfg[$part];
            }
            $count++;
        }
        return null;
    }

    /**
     * Set the configuration option based on the "path"
     * 
     * @param string $path Config "path" (Ex. "foo.bar.baz")
     * @param mixed $value Value of config
     */
    public function set($path, $value)
    {
        $p = explode('.', $path);
        $cfg = &$this->config;
        $count = 1;

        foreach ($p as $part) {
            if ($count == count($p)) {
                $cfg[$part] = $value;
                continue;
            }
            if (array_key_exists($part, $cfg)) {
                $cfg = &$cfg[$part];
            } else {
                // create the path
                $cfg[$part] = array();
                $cfg = &$cfg[$part];
            }
            $count++;
        }
    }
}