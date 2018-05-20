<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

interface RestaurantLoader
{

	public function loadRestaurant(): Restaurant;

}
