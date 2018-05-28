<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant\PragueKarlin;

use LunchCrawler\Restaurant\Restaurant;
use LunchCrawler\Restaurant\ZomatoRestaurantLoader;

final class Globus extends ZomatoRestaurantLoader
{

	public function getRestaurantId(): int
	{
		return 18257507;
	}

	public function getName(): string
	{
		return 'Restaurace Globus';
	}

	public function loadRestaurant(): Restaurant
	{
		self::$soapLimitPrice = 36;
		return parent::loadRestaurant();
	}

}
