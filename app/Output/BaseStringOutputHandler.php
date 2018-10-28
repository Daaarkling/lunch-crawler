<?php declare(strict_types = 1);

namespace LunchCrawler\Output;

use LunchCrawler\Output\Formatter\StringResultFormatter;

abstract class BaseStringOutputHandler implements OutputHandler
{

	/** @var \LunchCrawler\Output\Formatter\StringResultFormatter */
	protected $stringResultFormatter;

	public function __construct(StringResultFormatter $stringResultFormatter)
	{
		$this->stringResultFormatter = $stringResultFormatter;
	}

}
