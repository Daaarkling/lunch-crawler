<?php declare(strict_types = 1);

namespace LunchCrawler\Provider\Karlin;

use LunchCrawler\Restaurant\LinkRestaurantLoader;

final class PetersBurgerPub extends LinkRestaurantLoader
{

	public function getUrlMenu(): string
	{
		return 'https://www.facebook.com/petersburgerpub/';
	}

	public function getName(): string
	{
		return 'Peter\'s Pub';
	}

}
