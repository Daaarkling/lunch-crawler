<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use GuzzleHttp\Client;

abstract class HtmlParseRestaurantLoader implements RestaurantLoader
{

	/** @var \GuzzleHttp\Client */
	protected $httpClient;

	/** @var \LunchCrawler\Restaurant\RestaurantFormatter */
	protected $restaurantFormatter;

	public function __construct(Client $client, RestaurantFormatter $restaurantFormatter)
	{
		$this->httpClient = $client;
		$this->restaurantFormatter = $restaurantFormatter;
	}

}
