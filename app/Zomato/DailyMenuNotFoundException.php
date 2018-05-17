<?php declare(strict_types = 1);

namespace LunchCrawler\Zomato;

use Exception;
use Throwable;

class DailyMenuNotFoundException extends Exception
{

	public function __construct(int $id, ?Throwable $previous = null)
	{
		parent::__construct(sprintf('Daily menu for restaurant with Zomato id #%s was not found.', $id), 0, $previous);
	}

}
