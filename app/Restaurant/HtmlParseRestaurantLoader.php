<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use GuzzleHttp\Client;

abstract class HtmlParseRestaurantLoader implements RestaurantLoader
{

	/** @var \GuzzleHttp\Client */
	protected $httpClient;

	public function __construct(Client $client)
	{
		$this->httpClient = $client;
	}

}
