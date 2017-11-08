<?php

namespace Asahasrabuddhe\WorldData;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;
use ZipArchive;

trait GeoNames
{
	protected $url = 'http://download.geonames.org/export/dump/';

	protected $client;

	protected $startTime;

	protected $endTime;

	protected $runTime;

	protected function init()
	{
		$this->client = new Client([
			'base_uri' => $this->url
		]);
	}

	protected function start()
	{
		$this->startTime = microtime( true );
	}

	protected function stop()
	{
		$this->endTime = microtime( true );
		$this->runTime = $this->endTime - $this->startTime;
	}

	protected function getRunTime() :float
	{
		if( $this->runTime > 0 )
			return (float) $this->runTime;

		return (float) microtime( true ) - $this->startTime;
	}

	public function getAllLinks(): array
	{
		try
		{
			$response = $this->client->request('GET', '/');
		}
		catch(Exception $e)
		{
			echo 'error';
		}

		$crawler = new Crawler( $response->getBody()->getContents() );

		return $crawler->filter( 'a' )->each( function ( Crawler $node ) {
			return $node->attr( 'href' );
		} );
	}

	public function downloadFiles(Command $command, array $links): array
	{
		$localFilePaths = [];

		foreach($links as $link)
		{
			$localFilePaths[] = $this->downloadFile($command, $link);
		}

		return $localFilePaths;
	}

	public function downloadFile(Command $command, string $link): string
	{
		$localPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . basename( $link );

		$fileSize = $this->getFileSize($link);

		if( $fileSize > 0 )
		{
			$progress = $command->output->createProgressBar( $fileSize );
			$progress->setFormat( "\nDownloading %message% %current%/%max% [%bar%] %percent:3s%%\n" );
            $progress->setMessage( basename($link) );
		}
		else
		{
			$output->line( "\nWe were unable to get the file size of $link, so we will not display a progress bar. This could take a while, FYI.\n" );

		}

		try
		{
			$response = $this->client->request('GET', basename( $link ), [
				'sink' => $localPath,
				'progress' =>  function($downloadTotal, $downloadedBytes, $uploadTotal, $uploadedBytes) use ($progress) 
				{
					$progress->setProgress( $downloadedBytes );
				}
			]);
		}
		catch (Exception $e)
		{
			echo 'error';
		}

		return $localPath;
	}

	private function getFileSize(string $link): int
	{
		$result = -1;

		$response = $this->client->request('HEAD', basename($link));

		if( $response ) 
		{
            $content_length = current($response->getHeader('Content-Length'));
            $status = $response->getStatusCode();
            // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
            if( $status == 200 || ($status > 300 && $status <= 308) ) {
                $result = $content_length;
            }
        }

        return (int) $result;
	}

	public function unzipFiles( array $filePaths )
	{
		try
		{
			foreach( $filePaths as $filePath )
			{
				self::unzip( $filePath );
			}
		}
		catch (Exception $e)
		{
			echo 'error';
		}
	}

	public function unzip( string $filePath )
	{
		$zip = new ZipArchive;

		if( $zip->open( $filePath ) !== true )
		{
			echo 'error';
		}

		if( !file_exists(dirname($filePath)))
		{
			mkdir(dirname($filePath), 0775, true);
		}

		if( $zip->extractTo( dirname($filePath) . DIRECTORY_SEPARATOR) !== true )
		{
			echo 'error';
		}

		if( $zip->close() === false )
		{
			echo 'error';
		}

		return;
	}
}