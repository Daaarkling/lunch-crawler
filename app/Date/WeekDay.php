<?php declare(strict_types = 1);

namespace LunchCrawler\Date;

use function date;

class WeekDay
{

	// ISO-8601 numeric representation of the day of the week
	public const MONDAY = 1;
	public const TUESDAY = 2;
	public const WEDNESDAY = 3;
	public const THURSDAY = 4;
	public const FRIDAY = 5;
	public const SATURDAY = 6;
	public const SUNDAY = 7;

	/**
	 * @return string[]
	 */
	public static function getCzechNames(): array
	{
		return [
			self::MONDAY => 'Pondělí',
			self::TUESDAY => 'Úterý',
			self::WEDNESDAY => 'Středa',
			self::THURSDAY => 'Čtvrtek',
			self::FRIDAY => 'Pátek',
			self::SATURDAY => 'Sobota',
			self::SUNDAY => 'Neděle',
		];
	}

	public static function getCzechName(int $value): string
	{
		return self::getCzechNames()[$value];
	}

	public static function getCurrentCzechName(): string
	{
		return self::getCzechNames()[(int) date('N')];
	}

	public static function getTomorrowCzechName(): string
	{
		$current = (int) date('N');
		$tomorrow = ($current + 1) % 7;

		return self::getCzechNames()[$tomorrow];
	}

	public static function isFriday(): bool
	{
		return date('N') === self::FRIDAY;
	}

}
