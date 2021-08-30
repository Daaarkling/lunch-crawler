<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use LunchCrawler\Restaurant\Menu\Menu;

abstract class LinkRestaurantLoader implements RestaurantLoader
{

	public function loadRestaurant(): Restaurant
	{
		$menu = Menu::createFromUrl($this->getUrlMenu());

		return new Restaurant($this->getName(), $menu);
	}

	abstract public function getName(): string;

	abstract public function getUrlMenu(): string;

}
