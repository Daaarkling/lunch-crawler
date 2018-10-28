<?php declare(strict_types = 1);

namespace LunchCrawler\Output;

use Exception;
use Throwable;
use function sprintf;

class OutputOptionNotSupportedException extends Exception
{

	public function __construct(OutputOption $outputOption, ?Throwable $previous = null)
	{
		parent::__construct(sprintf('Output option #%s is not yet supported.', $outputOption->getValue()), 0, $previous);
	}

}
