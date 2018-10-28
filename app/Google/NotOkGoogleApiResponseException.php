<?php declare(strict_types = 1);

namespace LunchCrawler\Google;

use Exception;
use Throwable;
use function sprintf;

class NotOkGoogleApiResponseException extends Exception
{

	public function __construct(string $errorMsg, ?Throwable $previous = null)
	{
		parent::__construct(sprintf('Google Api answered with no ok response. Error message: %s', $errorMsg), 0, $previous);
	}

}
