<?php

namespace Asahasrabuddhe\WorldData;

use Asahasrabuddhe\WorldData\Support\CountryRepository;

class WorldDataService
{
    protected $countryRepository;

    public function __construct(CountryRepository $repository)
    {
        $this->countryRepository = $repository;
    }

    /**
     * Call a method.
     *
     * @param $name
     * @param array $arguments
     *
     * @return bool|mixed
     */
    public function __call($name, array $arguments = [])
    {
        $result = $this->countryRepository->call($name, $arguments);

        return $result;
    }
}
