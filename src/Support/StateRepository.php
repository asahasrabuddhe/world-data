<?php

namespace Asahasrabuddhe\WorldData\Support;

use Asahasrabuddhe\WorldData\Support\Collection;
use Asahasrabuddhe\WorldData\Support\Repository;

class StateRepository extends Repository
{
	protected $countryCode;

	/**
	 * Create a new State Repository instance
	 * 
	 * @param  string  $countryCode
	 * @return void
	 */
	public function __construct(string $countryCode)
	{
		$this->resourceKeys = [
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

		$this->countryCode = $countryCode;

		$this->load();
	}

	/**
	 * {@inheritdoc}
	 */
	public function load()
	{
		ini_set('memory_limit', -1);
		$f = fopen( dirname(dirname(__DIR__)) . '/data/' . $this->countryCode . '.txt', 'r' );
		if( $f )
		{
			while( ($line = fgets($f)) !== false )
			{
				$tmp = explode("\t", $line);
				$tmp = array_combine($this->resourceKeys, $tmp);
				if( $tmp['feature class'] == 'A' && $tmp['feature code'] == 'ADM1' )
					$this->resource[] = $tmp;
			}
		}
		fclose( $f );
		$this->resourceJson = json_encode($this->resource);
	}
}