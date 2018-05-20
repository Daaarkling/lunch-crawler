<?php declare(strict_types = 1);

namespace LunchCrawler;

use LunchCrawler\Restaurant\RestaurantEmptyMenuException;
use LunchCrawler\Restaurant\RestaurantLoadException;
use Symfony\Component\Console\Helper\ProgressBar;
use Tracy\Debugger;

class Crawler
{

	/** @var \Symfony\Component\Console\Helper\ProgressBar|null */
	private $progressBar = null;

	public function setProgressBar(ProgressBar $progressBar): void
	{
		$progressBar->setFormat('debug');
		$this->progressBar = $progressBar;
	}

	/**
	 * @param \LunchCrawler\Restaurant\RestaurantLoader[] $restaurantsLoaders
	 * @return \LunchCrawler\Result
	 */
	public function crawl(array $restaurantsLoaders): Result
	{
		if ($this->progressBar !== null) {
			$this->progressBar->start();
		}

		$restaurants = [];
		foreach ($restaurantsLoaders as $restaurantLoader) {
			try {
				$restaurants[] = $restaurantLoader->loadRestaurant();
			} catch (RestaurantEmptyMenuException $e) {
				Debugger::log($e);
			} catch (RestaurantLoadException $e) {
				Debugger::log($e);
			}

			if ($this->progressBar !== null) {
				$this->progressBar->advance();
			}
		}

		if ($this->progressBar !== null) {
			$this->progressBar->finish();
		}

		return new Result($restaurants, count($restaurantsLoaders));
	}

}
