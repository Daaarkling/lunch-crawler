<?php declare(strict_types = 1);

namespace LunchCrawler\Output;

use LunchCrawler\Output\Formatter\StringResultFormatter;
use LunchCrawler\Restaurant\RestaurantLoaderResult;
use Maknz\Slack\Client;

class SlackOutputHandler extends BaseStringOutputHandler
{

	/** @var \Maknz\Slack\Client */
	private $client;

	public function __construct(StringResultFormatter $stringResultFormatter, Client $client)
	{
		parent::__construct($stringResultFormatter);
		$this->client = $client;
	}

	public function handle(RestaurantLoaderResult $result): void
	{
		$toString = $this->stringResultFormatter->formatResultIntoString($result);
		$this->client->send($toString);
	}

}
