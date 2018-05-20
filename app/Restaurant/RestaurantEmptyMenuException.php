<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use Exception;
use Throwable;

class RestaurantEmptyMenuException extends Exception
{

	public function __construct(string $name, ?Throwable $previous = null)
	{
		parent::__construct(sprintf('Restaurant #%s has empty menu.', $name), 0, $previous);
	}

}
