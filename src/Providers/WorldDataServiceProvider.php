<?php

namespace Asahasrabuddhe\WorldData\Providers;

use Illuminate\Support\ServiceProvider;
use Asahasrabuddhe\WorldData\WorldDataService;
use Asahasrabuddhe\WorldData\Console\Command\DownloadDataCommand;
use Asahasrabuddhe\WorldData\Support\CountryRepository;

class WorldDataServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		if( $this->app->runningInConsole() )
		{
			$this->commands([
				DownloadDataCommand::class,
			]);
		}
	}

	public function register()
	{
		 $this->app->singleton('asahasrabuddhe.world-data', function () {
            return new WorldDataService(new CountryRepository());
        });
	}
}