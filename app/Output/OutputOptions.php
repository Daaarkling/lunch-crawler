<?php declare(strict_types = 1);

namespace LunchCrawler\Output;

class OutputOptions
{

	public const CONSOLE = 'console';
	public const SLACK = 'slack';
	public const OUTPUTS = [self::CONSOLE, self::SLACK];

	public static function isValid(string $option): bool
	{
		return in_array($option, self::OUTPUTS, true);
	}

}
