<?php declare(strict_types = 1);

namespace LunchCrawler\Zomato;

use GuzzleHttp\Client;
use Nette\Utils\Json;
use stdClass;

class ZomatoClient
{

	public const PRAGUE_CITY_ID = 84;

	private const API_URL = 'https://developers.zomato.com/api/v2.1/';
	private const API_URL_DAILY_MENU = '/dailymenu?res_id=';

	/** @var \GuzzleHttp\Client */
	private $httpClient;

	public function __construct(Client $httpClient)
	{
		$this->httpClient = $httpClient;
	}

	public function getDailyMenu(int $restaurantId): stdClass
	{
		$request = $this->httpClient->request(
			'GET',
			sprintf('%s%s%s', self::API_URL, self::API_URL_DAILY_MENU, $restaurantId)
		);

		if ($request->getStatusCode() !== 200) {
			throw new DailyMenuNotFoundException($restaurantId);
		}

		return Json::decode($request->getBody()->getContents());
	}

	public function getRestaurantId(string $name, int $cityId = self::PRAGUE_CITY_ID): int
	{
		return -1;
	}

}
