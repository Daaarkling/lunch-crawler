<?php declare(strict_types = 1);

namespace LunchCrawler\Distance\Calculator;

use Dogma\Geolocation\Position;
use LunchCrawler\Distance\Distance;

interface DistanceCalculator
{

	public function calculateDistance(Position $start, Position $end): Distance;

}
