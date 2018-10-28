<?php declare(strict_types = 1);

namespace LunchCrawler\Google;

use Exception;
use Throwable;
use function sprintf;

class BadGoogleApiRequestException extends Exception
{

	public function __construct(int $statusCode, ?Throwable $previous = null)
	{
		parent::__construct(sprintf('Google Api answered with invalid status code %s.', $statusCode), 0, $previous);
	}

}
