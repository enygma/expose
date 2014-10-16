<?php

namespace Expose;

abstract class Cache
{
	/**
	 * Save the data using the specified key
	 *
	 * @param string $key Key for identifying cache entry
	 * @param mixed $data Data to cache
	 * @return boolean Success/fail of save
	 */
	public abstract function save($key, $data);

	/**
	 * Get the cache record based on the key
	 *
	 * @param string $key Cache identifier key
	 * @return mixed Returns data if found, otherwise null
	 */
	public abstract function get($key);
}
