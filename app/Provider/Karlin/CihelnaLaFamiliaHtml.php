<?php declare(strict_types = 1);

namespace LunchCrawler\Provider\Karlin;

use Atrox\Matcher;
use Dogma\Geolocation\Position;
use LunchCrawler\Restaurant\HtmlParseRestaurantLoader;
use LunchCrawler\Restaurant\Menu\Dish;
use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Restaurant\Restaurant;
use LunchCrawler\Restaurant\RestaurantEmptyMenuException;
use LunchCrawler\Restaurant\RestaurantLoadException;
use Throwable;

final class CihelnaLaFamiliaHtml extends HtmlParseRestaurantLoader
{

	private const MENU_URL = 'http://www.cihelna-lafamilia.cz/';
	private const NAME = 'Cihelna La Familia';
	private const LAT = 50.0919221;
	private const LNG = 14.4468747;

	public function loadRestaurant(): Restaurant
	{
		try {
			$response = $this->httpClient->request('GET', self::MENU_URL);
			$html = $response->getBody()->getContents();

			$matcher = Matcher::single(
				'//div[@id="right"]//table',
				[
					'soap' => Matcher::single(
						'.//tr[1]',
						[
							'name' => './/div[@class="ingredience"]',
							'price' => './/td[@class="cena"]',
						]
					),
					'dish' => Matcher::multi(
						'.//tr[position()>1]',
						[
							'name' => './/div[@class="ingredience"]',
							'price' => './/td[@class="cena"]',
						]
					),
				]
			)->fromHtml();

			/** @var string[]&string[][] $rawDishes */
			$rawDishes = $matcher($html);

			$meals = [];
			$soaps = [];

			$soaps[] = new Dish($this->restaurantFormatter->sanitizeName($rawDishes['soap']['name']), (int) $rawDishes['soap']['price']);

			foreach ($rawDishes['dish'] as $rawDish) {
				$meals[] = new Dish($this->restaurantFormatter->sanitizeName($rawDish['name']), (int) $rawDish['price']);
			}

			$menu = Menu::createFromDishes($soaps, $meals);

			if ($menu->isEmpty()) {
				throw new RestaurantEmptyMenuException(self::NAME);
			}

			return new Restaurant(self::NAME, $menu, new Position(self::LAT, self::LNG));
		} catch (RestaurantEmptyMenuException $e) {
			throw $e;
		} catch (Throwable $e) {
			throw new RestaurantLoadException(self::NAME, $e);
		}
	}

}
