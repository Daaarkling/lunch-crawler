<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant\PragueKarlin;

use LunchCrawler\Restaurant\ZomatoRestaurantLoader;

final class PetersBurgerPub extends ZomatoRestaurantLoader
{

	private const ID = 16506740;
	private const NAME = 'Peter\'s Burger Pub';

	public function getRestaurantId(): int
	{
		return self::ID;
	}

	public function getName(): string
	{
		return self::NAME;
	}

}
