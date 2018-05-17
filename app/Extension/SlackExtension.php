<?php declare(strict_types = 1);

namespace LunchCrawler\Extension;

use Maknz\Slack\Client;
use Nette\DI\CompilerExtension;

class SlackExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaultConfig = [
		'endpoint' => null,
		'channel' => 'obed',
		'username' => 'LunchCrawler',
		'icon' => ':lunch:',
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaultConfig);

		$arguments['endpoint'] = $config['endpoint'];
		unset($config['endpoint']);
		$arguments['options'] = $config;
		$arguments['guzzle'] = '@' . \GuzzleHttp\Client::class;

		$builder->addDefinition($this->prefix('slackClient'))
			->setFactory(Client::class)
			->setArguments($arguments);
	}

}
