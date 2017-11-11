<?php

namespace Asahasrabuddhe\WorldData\Support;

use Asahasrabuddhe\WorldData\Support\Collection;

abstract class Repository
{
	/**
	 * Repository resource items in JSON Format
	 *
	 * @var  string
	 */
	protected $resourceJson;

	/**
	 * Repository resource items in Array Format
	 *
	 * @var  array
	 */
	protected $resource = [];

	/**
	 * Repository resource item keys
	 *
	 * @var  array
	 */
	protected $resourceKeys = [];

	/**
	 * Return a collection of items
	 *
	 * @param array $array
	 * @return \Asahasrabuddhe\WorldData\Support\Collection
	 */
	protected function collection($array)
	{
		return new Collection($array);
	}

	/**
	 * Get all resource items
	 *
	 * @return \Asahasrabuddhe\WorldData\Support\Collection
	 */
	public function all()
	{
		return $this->collection($this->resource);
	}

	/**
	 * Handle dynamic method calls into this repository
	 *
	 * @param  string  $name
	 * @param  array  $parameters
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		return call_user_func_array([$this->all(), $name], $arguments);
	}

	/**
	 * Call a method
	 *
	 * @param  string   $name
	 * @param  array $arguments
	 * @return bool|mixed
	 */
	public function call( $name, $arguments )
	{
		return call_user_func_array([$this, $name], $arguments);
	}

	/**
	 * Load Repository with data from source
	 *
	 * @return void
	 */
	abstract protected function load();
}