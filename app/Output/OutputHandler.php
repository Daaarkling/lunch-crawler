<?php declare(strict_types = 1);

namespace LunchCrawler\Output;

use LunchCrawler\Restaurant\RestaurantLoaderResult;

interface OutputHandler
{

	public function handle(RestaurantLoaderResult $result): void;

}
