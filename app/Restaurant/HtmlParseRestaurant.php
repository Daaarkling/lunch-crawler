<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use GuzzleHttp\Client;

abstract class HtmlParseRestaurant implements Restaurant
{

	/** @var \GuzzleHttp\Client */
	protected $httpClient;

	public function __construct()
	{
		$this->httpClient = new Client();
	}

}
