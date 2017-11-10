<?php

namespace Asahasrabuddhe\WorldData\Console\Command;

use Illuminate\Console\Command;
use Asahasrabuddhe\WorldData\GeoNames;
use Asahasrabuddhe\WorldData\WorldDataService;
use Asahasrabuddhe\WorldData\Support\CountryRepository;

class DownloadDataCommand extends Command
{
	use GeoNames;
	/**
     * The name and signature of the console command.
     *
     * @var string
     */
	protected $signature = 'world-data:install';

	 /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command installs the geonames.org dataset locally.";

    /**
     * @var array List of absolute local file paths to downloaded geonames files.
     */
    protected $localFiles = [];

    public function __construct()
    {
    	parent::__construct();
    	$this->init();
    }

	 /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        ini_set('memory_limit', -1);
        
        $countryInfo = 'countryInfo.txt';

        try {
            $this->downloadFile( $this, $countryInfo );
        } catch ( \Exception $e ) {
            $this->error( $e->getMessage() );
            return false;
        }

        $countries = new WorldDataService(new CountryRepository());

        $countryCodes = $countries->all()->pluck('ISO')->map(function( $item, $key ) {
            return $item . '.zip';
        });

        $statePaths = [];
        try {
            $statePaths[] = $this->downloadFiles( $this, $countryCodes->toArray() );
        } catch ( \Exception $e ) {
            $this->error( $e->getMessage() );
            return false;
        }

        $countryPaths = $countries->all()->pluck('ISO')->map(function( $item, $key ) {
            return dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $item . '.zip';
        });

        $this->unzipFiles($countryPaths->toArray());

        return true;
    }

}
