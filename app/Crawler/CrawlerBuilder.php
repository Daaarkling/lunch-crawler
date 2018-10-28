<?php declare(strict_types = 1);

namespace LunchCrawler\Crawler;

use Dogma\Geolocation\Position;
use LunchCrawler\Distance\DistanceFacade;
use Symfony\Component\Console\Helper\ProgressBar;

class CrawlerBuilder
{

	/** @var \Symfony\Component\Console\Helper\ProgressBar|null */
	private $progressBar;

	/** @var \Dogma\Geolocation\Position|null */
	private $startPosition;

	/** @var \LunchCrawler\Distance\DistanceFacade|null */
	private $distanceFacade;

	public function setProgressBar(ProgressBar $progressBar): CrawlerBuilder
	{
		$this->progressBar = $progressBar;
		return $this;
	}

	public function enableDistance(Position $startPosition, DistanceFacade $distanceFacade): CrawlerBuilder
	{
		$this->startPosition = $startPosition;
		$this->distanceFacade = $distanceFacade;
		return $this;
	}

	public function build(): Crawler
	{
		return new Crawler($this->progressBar, $this->startPosition, $this->distanceFacade);
	}

}
