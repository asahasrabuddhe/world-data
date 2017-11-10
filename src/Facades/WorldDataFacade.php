<?php

namespace Asahasrabuddhe\WorldData\Facades;

use Illuminate\Support\Facades\Facade;

class WorldDataFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'asahasrabuddhe.world-data';
    }
}
