<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use Nette\Utils\Strings;
use const PHP_SAPI;
use function utf8_decode;

class RestaurantFormatter
{

	public function sanitizeName(string $name): string
	{
		if ($this->isCliMode()) {
			return Strings::trim($name);
		}

		return Strings::trim(Strings::fixEncoding(utf8_decode($name)));
	}

	private function isCliMode(): bool
	{
		return PHP_SAPI === 'cli';
	}

}
