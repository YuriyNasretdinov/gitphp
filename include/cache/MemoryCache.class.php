<?php
/**
 * GitPHP MemoryCache
 *
 * Cache to manage objects in process memory
 *
 * @author Christopher Han <xiphux@gmail.com>
 * @copyright Copyright (c) 2012 Christopher Han
 * @package GitPHP
 * @subpackage Cache
 */

/**
 * MemoryCache class
 *
 * @package GitPHP
 * @subpackage Cache
 */
class GitPHP_MemoryCache
{
	/**
	 * instance
	 *
	 * Stores the singleton instance
	 *
	 * @access protected
	 * @static
	 */
	protected static $instance;

	/**
	 * objects
	 *
	 * Stores the objects in this cache
	 *
	 * @access protected
	 */
	protected $objects = array();

	/**
	 * cacheMap
	 *
	 * Map of objects in cache
	 *
	 * @access protected
	 */
	protected $cacheMap = array();

	/**
	 * size
	 *
	 * Size of cache
	 *
	 * @access protected
	 */
	protected $size;

	/**
	 * GetInstance
	 *
	 * Returns the singleton instance
	 *
	 * @access public
	 * @static
	 * @return mixed instance of config class
	 */
	public static function GetInstance()
	{
		if (!self::$instance) {
			self::$instance = new GitPHP_MemoryCache();
		}
		return self::$instance;
	}

	/**
	 * __construct
	 *
	 * Class constructor
	 *
	 * @access public
	 * @param int $size size of cache
	 */
	public function __construct($size = 0)
	{
		if ($size && ($size > 0)) {
			$this->size = $size;
		} else {
			$this->size = GitPHP_Config::GetInstance()->GetValue('objectmemory', 150);
		}
	}

	/**
	 * GetSize
	 *
	 * Gets the size of this cache
	 *
	 * @access public
	 * @return int size
	 */
	public function GetSize()
	{
		return $this->size;
	}

	/**
	 * SetSize
	 *
	 * Sets the size of this cache
	 *
	 * @access public
	 * @param int $size size
	 */
	public function SetSize($size)
	{
		if ($size && ($size > 0) && ($this->size != $size)) {
			$oldSize = $this->size;
			$this->size = $size;
			if ($size < $oldSize) {
				$this->Evict();
			}
		}
	}

	/**
	 * Get
	 *
	 * Gets an object from the cache
	 *
	 * @access public
	 * @param string $key cache key
	 * @return mixed object from cache if found
	 */
	public function Get($key)
	{
		if (empty($key))
			return null;

		if (!isset($this->objects[$key]))
			return null;

		$object = $this->objects[$key];

		//$this->KeyUsed($key);

		return $object;
	}

	/**
	 * Set
	 *
	 * Sets an object into the cache
	 *
	 * @access public
	 * @param string $key cache key
	 * @param mixed $object object to cache
	 */
	public function Set($key, $object)
	{
		if (empty($key))
			return;

		if (isset($this->objects[$key])) {
			$this->objects[$key] = $object;
			//$this->KeyUsed($key);
		} else {
			//$this->Evict();
			$this->objects[$key] = $object;
			//array_unshift($this->cacheMap, $key);
		}
	}

	/**
	 * Evict
	 *
	 * Evicts items from the cache down to the size limit
	 *
	 * @access private
	 */
	private function Evict()
	{
		while (count($this->cacheMap) >= $this->size) {
			$key = array_pop($this->cacheMap);

			if (!empty($key))
				unset($this->objects[$key]);
		}
	}

	/**
	 * KeyUsed
	 *
	 * Mark key as recently used
	 *
	 * @access private
	 */
	private function KeyUsed($key)
	{
		if (empty($key))
			return;

		$index = array_search($key, $this->cacheMap);
		if ($index !== false) {
			array_splice($this->cacheMap, $index, 1);
			array_unshift($this->cacheMap, $key);
		}
	}

}
