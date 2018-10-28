<?php declare(strict_types = 1);

namespace LunchCrawler\Output;

use Dogma\Enum\StringEnum;

class OutputOption extends StringEnum
{

	public const CONSOLE = 'console';
	public const SLACK = 'slack';

}
