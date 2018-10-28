<?php declare(strict_types = 1);

namespace LunchCrawler\Extension;

use GuzzleHttp\Client;
use LunchCrawler\Google\GoogleDistanceApiClient;
use Nette\DI\CompilerExtension;

class GoogleDistanceApiExtension extends CompilerExtension
{

	private const DEFAULTS = [
		'apiKey' => null,
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig(self::DEFAULTS);

		$arguments = [];
		$arguments['apiKey'] = $config['apiKey'];
		$arguments['httpClient'] = '@' . Client::class;

		$builder->addDefinition($this->prefix('googleDistanceApiClient'))
			->setFactory(GoogleDistanceApiClient::class)
			->setArguments($arguments);
	}

}
