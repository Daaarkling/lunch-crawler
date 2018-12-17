<?php declare(strict_types = 1);

namespace LunchCrawler\Zomato;

use Darkling\ZomatoClient\Request\Request;
use Darkling\ZomatoClient\Response\Response;
use Exception;
use Throwable;
use function get_class;
use function implode;
use function sprintf;

class ZomatoRequestFailedException extends Exception
{

	public function __construct(Request $request, Response $response, ?Throwable $previous = null)
	{
		parent::__construct(sprintf(
			'Request #%s with data (%s), responded with %d status code (%s).',
			get_class($request),
			implode(', ', $request->getParameters()),
			$response->getStatusCode(),
			$response->getReasonPhrase()
		), 0, $previous);
	}

}
