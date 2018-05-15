<?php declare(strict_types = 1);

namespace LunchCrawler\Slack;

use Maknz\Slack\Client;
use Throwable;

class SlackFactory
{

	public function create(string $configIniFile): Client
	{
		try {
			$config = parse_ini_file($configIniFile);
			return new Client($config['url'], $config);
		} catch (Throwable $e) {
			throw new SlackCreateException($configIniFile);
		}
	}

}
