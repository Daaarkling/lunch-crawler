<?php declare(strict_types = 1);

namespace LunchCrawler\Distance;

abstract class BaseDistanceFacade implements DistanceFacade
{

	/** @var \LunchCrawler\Distance\DistanceFacade */
	protected $distanceFacade;

	public function __construct(DistanceFacade $distanceFacade)
	{
		$this->distanceFacade = $distanceFacade;
	}

}
