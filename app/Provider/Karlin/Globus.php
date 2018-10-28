<?php declare(strict_types = 1);

namespace LunchCrawler\Provider\Karlin;

use Dogma\Geolocation\Position;
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

	public function getPosition(): Position
	{
		return new Position(50.0907588, 14.4352815);
	}

}
