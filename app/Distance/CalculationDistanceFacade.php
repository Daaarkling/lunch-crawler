<?php declare(strict_types = 1);

namespace LunchCrawler\Distance;

use Dogma\Geolocation\Position;
use LunchCrawler\Distance\Calculator\GoogleApiDistanceCalculator;
use LunchCrawler\Distance\Calculator\StraightLineDistanceCalculator;
use LunchCrawler\Restaurant\Restaurant;
use Throwable;
use Tracy\Debugger;

class CalculationDistanceFacade implements DistanceFacade
{

	/** @var \LunchCrawler\Distance\Calculator\DistanceCalculator */
	private $googleApiDistanceCalculator;

	/** @var \LunchCrawler\Distance\Calculator\StraightLineDistanceCalculator */
	private $straightLineDistanceCalculator;

	public function __construct(
		GoogleApiDistanceCalculator $googleApiDistanceCalculator,
		StraightLineDistanceCalculator $straightLineDistanceCalculator
	)
	{
		$this->googleApiDistanceCalculator = $googleApiDistanceCalculator;
		$this->straightLineDistanceCalculator = $straightLineDistanceCalculator;
	}

	public function getDistance(Position $startPosition, Restaurant $restaurant): Distance
	{
		$restaurantPosition = $restaurant->getPosition();

		try {
			$distance = $this->googleApiDistanceCalculator->calculateDistance($startPosition, $restaurantPosition);

		} catch (Throwable $e) {
			Debugger::log($e);
			$distance = $this->straightLineDistanceCalculator->calculateDistance($startPosition, $restaurantPosition);
		}

		return $distance;
	}

}
