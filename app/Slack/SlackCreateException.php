<?php declare(strict_types = 1);

namespace LunchCrawler\Slack;

use Exception;
use Throwable;

class SlackCreateException extends Exception
{

	public function __construct(string $configIniFile, ?Throwable $previous = null)
	{
		parent::__construct(
			sprintf('%s must be valid path to ini file and the file must contain at least `url` argument.', $configIniFile),
			0,
			$previous
		);
	}

}
