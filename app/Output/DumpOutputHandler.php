<?php declare(strict_types = 1);

namespace LunchCrawler\Output;

use LunchCrawler\Result;

class DumpOutputHandler implements OutputHandler
{

	public function handle(Result $result): void
	{
		foreach ($result->getMenu() as $menu) {
			var_dump($menu->getName());
			var_dump($menu->getSoups());
			var_dump($menu->getMeals());
		}
	}

}
