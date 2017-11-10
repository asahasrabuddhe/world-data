<?php

namespace Asahasrabuddhe\WorldData\Support;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\HigherOrderCollectionProxy;
use Illuminate\Support\Collection as IlluminateCollection;

class Collection extends IlluminateCollection
{
	private $stateCityKeys = [
		'geonameid',
		'name',
		'asciiname',
		'alternatenames',
		'latitude',
		'longitude',
		'feature class',
		'feature code',
		'country code',
		'cc2',
		'admin1 code',
		'admin2 code',
		'admin3 code',
		'admin4 code',
		'population',
		'elevation',
		'dem',
		'timezone',
		'modification date'
	];
	/**
     * Collection constructor.
     * @param array $items
     */
    public function __construct($items = [])
    {
        parent::__construct($items);

        $this->registerMacros();
    }


	public function registerMacros()
    {
    	static::macro('states', function() {
    		ini_set('memory_limit', -1);
    		$f = fopen( dirname(dirname(__DIR__)) . '/data/' . $this->pluck('ISO')[0] . '.txt', 'r' );
    		$state = [];
    		if( $f )
			{
				while( ($line = fgets($f)) !== false )
				{
					$tmp = explode("\t", $line);
					$tmp = array_combine($this->stateCityKeys, $tmp);
					if( $tmp['feature class'] == 'A' && $tmp['feature code'] == 'ADM1' )
						$state[] = $tmp;
				}
			}
    		return new Collection($state);
    	});

    	static::macro('cities', function() {
    		ini_set('memory_limit', -1);
    		$f = fopen( dirname(dirname(__DIR__)) . '/data/' . $this->pluck('country code')[0] . '.txt', 'r' );
    		$state = [];
    		if( $f )
			{
				while( ($line = fgets($f)) !== false )
				{
					$tmp = explode("\t", $line);
					$tmp = array_combine($this->stateCityKeys, $tmp);
					if( $tmp['feature class'] == 'P' && $tmp['admin1 code'] == $this->pluck('admin1 code')[0] )
						$state[] = $tmp;
				}
			}
    		return new Collection($state);
    	});
    }

    /**
     * Take the first item.
     *
     * @param callable|null $callback
     * @param null $default
     * @return mixed|Collection
     */
    public function first(callable $callback = null, $default = null)
    {
        return $this->make(parent::first($callback, $default));
    }

    /**
     * Dynamically access collection proxies.
     *
     * @param  string  $key
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->{$key};
        }

        if (isset($this->items[$key])) {
            if (is_array($this->items[$key])) {
                return $this->make($this->items[$key]);
            }

            return $this->items[$key];
        }

        if (! in_array($key, static::$proxies)) {
            throw new Exception("Property [{$key}] does not exist on this collection instance.");
        }

        return new HigherOrderCollectionProxy($this, $key);
    }

    public function __call($name, $arguments)
    {
        if (starts_with($name, 'where')) {
            $name = strtolower(preg_replace('/([A-Z])/', '.$1', lcfirst(substr($name, 5))));
            if (count($arguments) == 2) {
                return $this->where($name, $arguments[0], $arguments[1]);
            } elseif (count($arguments) == 1) {
                return $this->where($name, $arguments[0]);
            }
        }

        return parent::__call($name, $arguments);
    }

    public function where($key, $operator, $value = null)
    {
        if (func_num_args() == 2) {
            $value = $operator;

            $operator = '=';
        }
        // if (array_key_exists($key, config('countries.maps'))) {
        //     $key = config('countries.maps')[$key];
        // }

        if (method_exists($this, 'where'.ucfirst($key))) {
            return $this->{'where'.ucfirst($key)}($value);
        }

        return parent::where($key, $operator, $value);
    }
}