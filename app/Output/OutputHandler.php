<?php declare(strict_types = 1);

namespace LunchCrawler\Output;

use LunchCrawler\Result;

interface OutputHandler
{

	public function handle(Result $result): void;

}
