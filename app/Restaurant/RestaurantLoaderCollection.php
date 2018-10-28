<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

class RestaurantLoaderCollection
{

	/** @var \LunchCrawler\Restaurant\RestaurantLoader[] */
	private $restaurantLoaders;

	public function addRestaurantLoader(RestaurantLoader $restaurant): void
	{
		$this->restaurantLoaders[] = $restaurant;
	}

	public function getCount(): int
	{
		return count($this->restaurantLoaders);
	}

	/**
	 * @return \LunchCrawler\Restaurant\RestaurantLoader[]
	 */
	public function getRestaurantLoaders(): array
	{
		return $this->restaurantLoaders;
	}

}
