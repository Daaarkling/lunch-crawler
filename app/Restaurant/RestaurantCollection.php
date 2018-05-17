<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

class RestaurantCollection
{

	/** @var \LunchCrawler\Restaurant\Restaurant[] */
	private $restaurants;

	public function addRestaurant(Restaurant $restaurant): void
	{
		$this->restaurants[] = $restaurant;
	}

	public function getCount(): int
	{
		return count($this->restaurants);
	}

	/**
	 * @return \LunchCrawler\Restaurant\Restaurant[]
	 */
	public function getRestaurants(): array
	{
		return $this->restaurants;
	}

	/**
	 * @param \LunchCrawler\Restaurant\Restaurant[] $restaurants
	 */
	public function setRestaurants(array $restaurants): void
	{
		$this->restaurants = $restaurants;
	}

}
