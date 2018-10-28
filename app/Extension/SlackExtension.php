<?php declare(strict_types = 1);

namespace LunchCrawler\Extension;

use Maknz\Slack\Client;
use Nette\DI\CompilerExtension;

class SlackExtension extends CompilerExtension
{

	private const DEFAULTS = [
		'endpoint' => null,
		'channel' => 'obed',
		'username' => 'LunchCrawler',
		'icon' => ':lunch:',
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig(self::DEFAULTS);

		$arguments = [];
		$arguments['endpoint'] = $config['endpoint'];
		unset($config['endpoint']);
		$arguments['attributes'] = $config;
		$arguments['guzzle'] = '@' . \GuzzleHttp\Client::class;

		$builder->addDefinition($this->prefix('slackClient'))
			->setFactory(Client::class)
			->setArguments($arguments);
	}

}
