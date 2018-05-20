<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant\PragueKarlin;

use LunchCrawler\Restaurant\ZomatoRestaurantLoader;

final class HostinecUTunelu extends ZomatoRestaurantLoader
{

	public function getRestaurantId(): int
	{
		return 16524768;
	}

	public function getName(): string
	{
		return 'Hostinec U Tunelu';
	}

}
