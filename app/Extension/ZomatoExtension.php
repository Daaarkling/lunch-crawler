<?php declare(strict_types = 1);

namespace LunchCrawler\Extension;

use GuzzleHttp\Client;
use LunchCrawler\Zomato\ZomatoClient;
use Nette\DI\CompilerExtension;

class ZomatoExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaultConfig = [
		'user_key' => null,
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaultConfig);

		$builder->addDefinition($this->prefix('guzzle'))
			->setFactory(Client::class)
			->setAutowired(false)
			->setArguments([
				'config' => [
					'headers' => [
						'user_key' => $config['user_key'],
					],
				],
			]);

		$builder->addDefinition($this->prefix('zomatoClient'))
			->setFactory(ZomatoClient::class)
			->setArguments(['@' . $this->prefix('guzzle')]);
	}

}
