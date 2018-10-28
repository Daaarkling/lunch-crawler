<?php declare(strict_types = 1);

namespace LunchCrawler\Output;

use LunchCrawler\Restaurant\RestaurantLoaderResult;

class ConsoleOutputHandler extends BaseStringOutputHandler
{

	public function handle(RestaurantLoaderResult $result): void
	{
		echo $this->stringResultFormatter->formatResultIntoString($result);
	}

}
