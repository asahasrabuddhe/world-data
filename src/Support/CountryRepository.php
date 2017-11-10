<?php

namespace Asahasrabuddhe\WorldData\Support;

class CountryRepository
{
    protected $countriesJson;

    protected $countries = [];

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
        'equivalentFipsCode',
    ];

    public function __construct()
    {
        $this->loadCountries();
    }

    public function call($name, $arguments)
    {
        $result = call_user_func_array([$this, $name], $arguments);

        return $result;
    }

    public function collection($country)
    {
        return new Collection($country);
    }

    public function loadCountries()
    {
        $f = fopen(dirname(dirname(__DIR__)).'/data/countryInfo.txt', 'r');
        if ($f) {
            while (($line = fgets($f)) !== false) {
                if ($line[0] == '#') {
                    continue;
                }
                $tmp = explode("\t", $line);
                $tmp = array_combine($this->countryKeys, $tmp);
                $this->countries[] = new Collection($tmp);
            }
        }
        $this->countriesJson = json_encode($this->countries);
    }

    public function all()
    {
        return $this->collection($this->countries);
    }

    public function __call($name, array $arguments = [])
    {
        return call_user_func_array([$this->all(), $name], $arguments);
    }
}
