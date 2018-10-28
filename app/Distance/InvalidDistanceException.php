<?php declare(strict_types = 1);

namespace LunchCrawler\Distance;

use Exception;
use Throwable;
use function sprintf;

class InvalidDistanceException extends Exception
{

	public function __construct(float $meters, ?Throwable $previous = null)
	{
		parent::__construct(sprintf('Distance can not be less then 0. Given values meters: %d.', $meters), 0, $previous);
	}

}
