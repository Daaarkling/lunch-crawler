<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

class RestaurantLoaderCollection
{

	/** @var \LunchCrawler\Restaurant\RestaurantLoader[] */
	private $restaurants;

	public function addRestaurant(RestaurantLoader $restaurant): void
	{
		$this->restaurants[] = $restaurant;
	}

	public function getCount(): int
	{
		return count($this->restaurants);
	}

	/**
	 * @return \LunchCrawler\Restaurant\RestaurantLoader[]
	 */
	public function getRestaurants(): array
	{
		return $this->restaurants;
	}

	/**
	 * @param \LunchCrawler\Restaurant\RestaurantLoader[] $restaurants
	 */
	public function setRestaurants(array $restaurants): void
	{
		$this->restaurants = $restaurants;
	}

}
