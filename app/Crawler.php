<?php declare(strict_types = 1);

namespace LunchCrawler;

use LunchCrawler\Restaurant\EmptyRestaurantException;
use LunchCrawler\Restaurant\RestaurantParseException;
use Symfony\Component\Console\Helper\ProgressBar;
use Tracy\Debugger;

class Crawler
{

	/** @var \Symfony\Component\Console\Helper\ProgressBar */
	private $progressBar;

	public function __construct(ProgressBar $progressBar)
	{
		$progressBar->setFormat('debug');
		$this->progressBar = $progressBar;
	}

	/**
	 * @param \LunchCrawler\Restaurant\Restaurant[] $restaurants
	 * @return \LunchCrawler\Result
	 */
	public function crawl(array $restaurants): Result
	{
		$this->progressBar->start();

		$menu = [];
		foreach ($restaurants as $restaurant) {
			try {
				$loadedMenu = $restaurant->loadMenu();
				if (!$loadedMenu->isEmpty()) {
					$menu[] = $loadedMenu;
				}

				Debugger::log(new EmptyRestaurantException($loadedMenu->getName()));

			} catch (RestaurantParseException $e) {
				Debugger::log($e);
			}
			$this->progressBar->advance();
		}
		$this->progressBar->finish();

		return new Result($menu, count($restaurants));
	}

}
