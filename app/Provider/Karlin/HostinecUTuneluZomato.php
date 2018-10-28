<?php declare(strict_types = 1);

namespace LunchCrawler\Provider\Karlin;

use Dogma\Geolocation\Position;
use LunchCrawler\Restaurant\ZomatoRestaurantLoader;

final class HostinecUTuneluZomato extends ZomatoRestaurantLoader
{

	public function getRestaurantId(): int
	{
		return 16524768;
	}

	public function getName(): string
	{
		return 'Hostinec U Tunelu';
	}

	public function getUrlMenu(): string
	{
		return 'http://www.utunelu.cz/denni_menu.pdf';
	}

	public function getPosition(): Position
	{
		return new Position(50.090616, 14.4509613);
	}

}
