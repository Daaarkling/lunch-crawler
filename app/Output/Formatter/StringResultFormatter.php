<?php declare(strict_types = 1);

namespace LunchCrawler\Output\Formatter;

use LunchCrawler\Restaurant\RestaurantLoaderResult;

interface StringResultFormatter
{

	public function formatResultIntoString(RestaurantLoaderResult $result): string;

}
