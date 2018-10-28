<?php declare(strict_types = 1);

namespace LunchCrawler\Crawler;

use Dogma\Geolocation\Position;
use LunchCrawler\Distance\DistanceFacade;
use LunchCrawler\Restaurant\RestaurantEmptyMenuException;
use LunchCrawler\Restaurant\RestaurantLoaderResult;
use LunchCrawler\Restaurant\RestaurantLoadException;
use Symfony\Component\Console\Helper\ProgressBar;
use Tracy\Debugger;
use function get_class;

class Crawler
{

	/** @var \Symfony\Component\Console\Helper\ProgressBar|null */
	private $progressBar;

	/** @var \Dogma\Geolocation\Position|null */
	private $startPosition;

	/** @var \LunchCrawler\Distance\DistanceFacade|null */
	private $distanceFacade;

	public function __construct(
		?ProgressBar $progressBar,
		?Position $startPosition,
		?DistanceFacade $distanceFacade
	)
	{
		$this->progressBar = $progressBar;
		$this->startPosition = $startPosition;
		$this->distanceFacade = $distanceFacade;
	}

	/**
	 * @param \LunchCrawler\Restaurant\RestaurantLoader[] $restaurantsLoaders
	 * @return \LunchCrawler\Restaurant\RestaurantLoaderResult
	 */
	public function crawl(array $restaurantsLoaders): RestaurantLoaderResult
	{
		if ($this->progressBar !== null) {
			$this->progressBar->start();
		}

		$successful = [];
		$failed = [];

		foreach ($restaurantsLoaders as $restaurantLoader) {
			try {
				$successful[] = $restaurant = $restaurantLoader->loadRestaurant();

				if ($this->startPosition !== null && $this->distanceFacade !== null) {
					$distance = $this->distanceFacade->getDistance($this->startPosition, $restaurant);
					$restaurant->setDistance($distance);
				}
			} catch (RestaurantEmptyMenuException $e) {
				Debugger::log($e);
				$failed[] = get_class($restaurantLoader);

			} catch (RestaurantLoadException $e) {
				Debugger::log($e);
				$failed[] = get_class($restaurantLoader);
			}

			if ($this->progressBar !== null) {
				$this->progressBar->advance();
			}
		}

		if ($this->progressBar !== null) {
			$this->progressBar->finish();
		}

		return new RestaurantLoaderResult($successful, $failed);
	}

}
