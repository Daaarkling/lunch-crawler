<?php declare(strict_types = 1);

namespace LunchCrawler\Google;

use Dogma\Enum\StringEnum;

class DistanceUnits extends StringEnum
{

	public const METRIC = 'metric';
	public const IMPERIAL = 'imperial';

}
