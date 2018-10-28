<?php declare(strict_types = 1);

namespace LunchCrawler\Distance\Calculator;

use Dogma\Geolocation\Position;
use LunchCrawler\Distance\Distance;
use function atan2;
use function cos;
use function deg2rad;
use function sin;
use function sqrt;

/*
 * http://www.movable-type.co.uk/scripts/latlong.html
 */
class StraightLineDistanceCalculator implements DistanceCalculator
{

	public function calculateDistance(Position $start, Position $end): Distance
	{
		[$startLatRadians, $startLngRadians] = $this->toRadians($start);
		[$endLatRadians, $endLngRadians] = $this->toRadians($end);

		$deltaLat = $endLatRadians - $startLatRadians;
		$deltaLng = $endLngRadians - $startLngRadians;

		$a = (sin($deltaLat / 2) ** 2) + cos($startLatRadians) * cos($endLatRadians) * (sin($deltaLng / 2) ** 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		$distance = Position::PLANET_EARTH_RADIUS * $c;

		return new Distance($start, $end, (int) $distance);
	}

	/**
	 * @param \Dogma\Geolocation\Position $position
	 * @return float[]
	 */
	private function toRadians(Position $position): array
	{
		return [
			deg2rad($position->getLatitude()),
			deg2rad($position->getLatitude()),
		];
	}

}
