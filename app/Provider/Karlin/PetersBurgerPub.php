<?php declare(strict_types = 1);

namespace LunchCrawler\Provider\Karlin;

use LunchCrawler\Restaurant\ZomatoRestaurantLoader;

final class PetersBurgerPub extends ZomatoRestaurantLoader
{

	public function getRestaurantId(): int
	{
		return 16506740;
	}

	public function getName(): string
	{
		return 'Peter\'s Burger Pub';
	}

}
