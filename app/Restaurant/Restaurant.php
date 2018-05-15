<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use LunchCrawler\Restaurant\Menu\Menu;

interface Restaurant
{

	public function loadMenu(): Menu;

}
