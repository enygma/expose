<?php

namespace Expose\Cache;

class File extends \Expose\Cache
{
	/**
	 * Path for cache files
	 * @var string
	 */
	private $path = '/tmp';

	/**
	 * Save the cache data to a file
	 *
	 * @param string $key Identifier key (used in filename)
	 * @param mixed $data Data to cache
	 * @return boolean Success/fail of save
	 */
	public function save($key, $data)
	{
		$hash = md5($key);
		$cacheFile = $this->getPath().'/'.$hash.'.cache';

		return file_put_contents($cacheFile, serialize($data));
	}

	/**
	 * Get the record identified by the given key
	 *
	 * @param string $key Cache identifier key
	 * @return mixed Returns either data or null if not found
	 */
	public function get($key)
	{
		$hash = md5($key);
		$cacheFile = $this->getPath().'/'.$hash.'.cache';

		if (!is_file($cacheFile)) {
			return false;
		}
        $t = file_get_contents($cacheFile);
        return (false !== $t) ? unserialize($t) : false;
	}

	/**
	 * Set the path to save cache files into
	 *
	 * @param string $path File system path
	 */
	public function setPath($path)
	{
		if (!is_writable($path)) {
			throw new \InvalidArgumentException('Cannot write to path '.$path);
		}
		$this->path = $path;
	}

	/**
	 * Get the current cache file path
	 *
	 * @return string Hash directory path
	 */
	public function getPath()
	{
		return $this->path;
	}
}
