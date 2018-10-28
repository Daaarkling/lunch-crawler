<?php declare(strict_types = 1);

namespace LunchCrawler\Distance;

use Dogma\Geolocation\Position;

class Distance
{

	/** @var \Dogma\Geolocation\Position */
	private $start;

	/** @var \Dogma\Geolocation\Position */
	private $end;

	/** @var int */
	private $meters;

	public function __construct(Position $start, Position $end, int $meters)
	{
		if ($meters < 0) {
			throw new InvalidDistanceException($meters);
		}

		$this->start = $start;
		$this->end = $end;
		$this->meters = $meters;
	}

	public function getStart(): Position
	{
		return $this->start;
	}

	public function getEnd(): Position
	{
		return $this->end;
	}

	public function getMeters(): int
	{
		return $this->meters;
	}

}
