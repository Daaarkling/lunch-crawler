<?php declare(strict_types = 1);

namespace LunchCrawler\Date;

use DateTimeImmutable;
use Dogma\Enum\IntEnum;

class CzechMonths extends IntEnum
{

	public const JANUARY = 1;
	public const FEBRUARY = 2;
	public const MARCH = 3;
	public const APRIL = 4;
	public const MAY = 5;
	public const JUNE = 6;
	public const JULY = 7;
	public const AUGUST = 8;
	public const SEPTEMBER = 9;
	public const OCTOBER = 10;
	public const NOVEMBER = 11;
	public const DECEMBER = 12;

	/**
	 * @return string[]
	 */
	public static function getLabels(): array
	{
		return [
			self::JANUARY => _('Leden'),
			self::FEBRUARY => _('Únor'),
			self::MARCH => _('Březen'),
			self::APRIL => _('Duben'),
			self::MAY => _('Květen'),
			self::JUNE => _('Červen'),
			self::JULY => _('Červenec'),
			self::AUGUST => _('Srpen'),
			self::SEPTEMBER => _('Září'),
			self::OCTOBER => _('Říjen'),
			self::NOVEMBER => _('Listopad'),
			self::DECEMBER => _('Prosinec'),
		];
	}

	public function getLabel(): string
	{
		return self::getLabels()[$this->getValue()];
	}

	/**
	 * @return string[]
	 */
	public static function getGenitives(): array
	{
		return [
			self::JANUARY => _('Ledna'),
			self::FEBRUARY => _('Února'),
			self::MARCH => _('Března'),
			self::APRIL => _('Dubna'),
			self::MAY => _('Května'),
			self::JUNE => _('Června'),
			self::JULY => _('Července'),
			self::AUGUST => _('Srpna'),
			self::SEPTEMBER => _('Září'),
			self::OCTOBER => _('Října'),
			self::NOVEMBER => _('Listopadu'),
			self::DECEMBER => _('Prosince'),
		];
	}

	public function getGenetiv(): string
	{
		return self::getGenitives()[$this->getValue()];
	}

	public static function getFromDateTime(DateTimeImmutable $dateTimeImmutable): CzechMonths
	{
		return self::get((int) $dateTimeImmutable->format('n'));
	}

}
