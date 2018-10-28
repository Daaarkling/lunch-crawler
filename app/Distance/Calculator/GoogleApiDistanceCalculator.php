<?php declare(strict_types = 1);

namespace LunchCrawler\Distance\Calculator;

use Dogma\Geolocation\Position;
use LunchCrawler\Distance\Distance;
use LunchCrawler\Google\GoogleDistanceApiClient;

class GoogleApiDistanceCalculator implements DistanceCalculator
{

	/** @var \LunchCrawler\Google\GoogleDistanceApiClient */
	private $googleDistanceApiClient;

	public function __construct(GoogleDistanceApiClient $googleDistanceApiClient)
	{
		$this->googleDistanceApiClient = $googleDistanceApiClient;
	}

	public function calculateDistance(Position $start, Position $end): Distance
	{
		$jsonResponse = $this->googleDistanceApiClient->getDistance($start, $end);
		return new Distance($start, $end, (int) $jsonResponse['rows']['elements'][0]['distance']['value']);
	}

}
