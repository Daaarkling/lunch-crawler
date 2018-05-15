<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use Exception;
use Throwable;

class RestaurantParseException extends Exception
{

	public function __construct(string $name, ?Throwable $previous = null)
	{
		parent::__construct(sprintf('Menu at #%s restaurant could not be parsed.', $name), 0, $previous);
	}

}
