<?php declare(strict_types = 1);

namespace LunchCrawler\Distance;

use Dogma\Geolocation\Position;
use LunchCrawler\Restaurant\Restaurant;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Utils\Strings;
use function implode;
use function sprintf;

class CacheDistanceFacade extends BaseDistanceFacade
{

	private const CACHE_KEY = 'distance';

	/** @var \Nette\Caching\Cache */
	private $cache;

	public function __construct(DistanceFacade $distanceFacade, IStorage $storage)
	{
		parent::__construct($distanceFacade);

		$this->cache = new Cache($storage);
	}

	public function getDistance(Position $startPosition, Restaurant $restaurant): Distance
	{
		$cacheKey = sprintf(
			'%s-%s-%s',
			self::CACHE_KEY,
			implode('-', $startPosition->export()),
			Strings::webalize($restaurant->getName())
		);
		$distance = $this->cache->load($cacheKey);

		if ($distance !== null) {
			return $distance;
		}

		$distance = $this->distanceFacade->getDistance($startPosition, $restaurant);

		$this->cache->save($cacheKey, $distance);
		return $distance;
	}

}
