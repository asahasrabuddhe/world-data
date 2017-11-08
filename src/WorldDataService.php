<?php

namespace Asahasrabuddhe\WorldData;

class WorldDataService
{
	protected $countryKeys = [
		'ISO',
		'ISO3',
		'ISO_Numeric',
		'fips',
		'name',
		'capital',
		'area',
		'population',
		'continent',
		'tld',
		'currencyCode',
		'currencyName',
		'phone',
		'postalCodeFormat',
		'postalCodeRegex',
		'languages',
		'geonameid',
		'neighbours',
		'equivalentFipsCode'
	];

	protected $stateCityKeys = [
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

	protected $countries;

	public function all()
	{
		$f = fopen( dirname(__DIR__) . '/data/countryInfo.txt', 'r' );
		$cnt = [];

		if( $f )
		{
			while( ($line = fgets($f)) !== false )
			{
				if( $line[0] == '#' )
					continue;
				$tmp = explode("\t", $line);
				$tmp = array_combine($this->countryKeys, $tmp);
				$cnt[] = $tmp;
			}
		}
		fclose( $f );

		$this->countries = collect($cnt);

		return $this->countries;
	}

	public function states()
	{
		return 'abc';
	}

	public function __call($name, $arguments)
	{
		if($name == 'states')
		{
			return call_user_func_array([$this, $name], $arguments);
		}
		else
		{
			return call_user_func_array([$this->all(), $name], $arguments);
		}
	}
}