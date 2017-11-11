<?php

namespace Asahasrabuddhe\WorldData\Support;

use Asahasrabuddhe\WorldData\Support\Collection;
use Asahasrabuddhe\WorldData\Support\Repository;

use Asahasrabuddhe\WorldData\Support\StateRepository;

class CountryRepository extends Repository
{
	protected $stateRepository;

	public function __construct()
	{
		$this->resourceKeys = [
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

		$this->load();
	}

	/**
	 * {@inheritdoc}
	 */
	public function load()
	{
		ini_set('memory_limit', '-1');
		$f = fopen( dirname(dirname(__DIR__)) . '/data/countryInfo.txt', 'r' );
		if( $f )
		{
			while( ($line = fgets($f)) !== false )
			{
				if( $line[0] == '#' )
					continue;
				$tmp = explode("\t", $line);
				$tmp = array_combine($this->resourceKeys, $tmp);
				$this->resource[] = new Collection($tmp);
			}
		}
		fclose( $f );
		$this->resourceJson = json_encode($this->resource);
	}

	public function states(string $countryCode)
	{
		$this->stateRepository = new StateRepository($countryCode);
		return $this->stateRepository->all();
	}
}