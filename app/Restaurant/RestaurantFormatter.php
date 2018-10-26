<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use Nette\Utils\Strings;
use function utf8_decode;

class RestaurantFormatter
{

	public static function format(string $name): string
	{
		if (self::isCliMode()) {
			return Strings::trim($name);
		}

		return Strings::trim(Strings::fixEncoding(utf8_decode($name)));
	}

	private static function isCliMode(): bool
	{
		return PHP_SAPI === 'cli';
	}

}
