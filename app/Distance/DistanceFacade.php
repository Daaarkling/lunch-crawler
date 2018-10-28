<?php declare(strict_types = 1);

namespace LunchCrawler\Distance;

use Dogma\Geolocation\Position;
use LunchCrawler\Restaurant\Restaurant;

interface DistanceFacade
{

	public function getDistance(Position $startPosition, Restaurant $restaurant): Distance;

}
