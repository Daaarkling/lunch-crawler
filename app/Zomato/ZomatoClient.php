<?php declare(strict_types = 1);

namespace LunchCrawler\Zomato;

use GuzzleHttp\Client;
use Nette\Http\Url;
use Nette\Utils\Json;
use stdClass;

class ZomatoClient
{

	public const PRAGUE_CITY_ID = 84;

	private const URL_BASE = 'https://developers.zomato.com/api/v2.1/';
	private const URL_DAILY_MENU = '/dailymenu';
	private const URL_SEARCH = '/search';

	/** @var \GuzzleHttp\Client */
	private $httpClient;

	public function __construct(Client $httpClient)
	{
		$this->httpClient = $httpClient;
	}

	public function getDailyMenu(int $restaurantId): stdClass
	{
		$url = new Url(self::URL_BASE . self::URL_DAILY_MENU);
		$url->appendQuery([
			'res_id' => $restaurantId,
		]);

		$response = $this->httpClient->request('GET', $url->getAbsoluteUrl());

		if ($response->getStatusCode() !== 200) {
			throw new DailyMenuNotFoundException($restaurantId);
		}

		return Json::decode($response->getBody()->getContents());
	}

	/**
	 * @param string $name
	 * @param int $cityId
	 * @return string[]
	 */
	public function getRestaurantId(string $name, int $cityId = self::PRAGUE_CITY_ID): array
	{
		$url = new Url(self::URL_BASE . self::URL_SEARCH);
		$url->appendQuery([
			'entity_type' => 'city',
			'entity_id' => $cityId,
			'count' => 5,
			'q' => $name,
		]);

		$response = $this->httpClient->request('GET', $url->getAbsoluteUrl());

		if ($response->getStatusCode() !== 200) {
			return [];
		}

		$data = Json::decode($response->getBody()->getContents());

		$possibilities = [];
		foreach ($data->restaurants as $restaurant) {
			$possibilities[] = [
				'id' => $restaurant->restaurant->id,
				'name' => $restaurant->restaurant->name,
				'url' => $restaurant->restaurant->url,
			];
		}

		return $possibilities;
	}

}
