<?php declare(strict_types = 1);

namespace LunchCrawler\Provider\Karlin;

use Atrox\Matcher;
use function dump;
use LunchCrawler\Restaurant\HtmlParseRestaurantLoader;
use LunchCrawler\Restaurant\Menu\Dish;
use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Restaurant\Restaurant;
use LunchCrawler\Restaurant\RestaurantEmptyMenuException;
use LunchCrawler\Restaurant\RestaurantLoadException;
use Throwable;

final class PivoKarlin extends HtmlParseRestaurantLoader
{

	private const SOAP_MAX_PRICE = 50;
	private const SOAP_MIN_PRICE = 40;
	private const MENU_URL = 'http://www.pivokarlin.cz/';
	private const NAME = 'Pivo Karlín';

	public function loadRestaurant(): Restaurant
	{
		try {
			$response = $this->httpClient->request('GET', self::MENU_URL);
			$html = $response->getBody()->getContents();

			$matcher = Matcher::multi('//div[@id="menuModala"]//div[contains(@class, "menu")]/div', [
				'name' => './/div[contains(@class, "col-sm-10")]',
				'price' => './/div[contains(@class, "col-sm-2")]',
			])->fromHtml();

			/** @var string[][] $rawDishes */
			$rawDishes = $matcher($html);

			$soaps = [];
			$meals = [];

			foreach ($rawDishes as $rawDish) {
				$rawName = $rawDish['name'];
				if ($rawName === null || $rawName === '') {
					continue;
				}

				$name = $this->restaurantFormatter->sanitizeName($rawDish['name']);
				$price = (int) $rawDish['price'];

				if ($name === '' || $price === 0) {
					continue;
				}

				if ($price < self::SOAP_MIN_PRICE) {
					continue;
				} elseif ($price >= self::SOAP_MIN_PRICE && $price <= self::SOAP_MAX_PRICE) {
					$soaps[] = new Dish($name, $price);

				} else {
					$meals[] = new Dish($name, $price);
				}
			}

			$menu = Menu::createFromDishes($soaps, $meals);

			if ($menu->isEmpty()) {
				throw new RestaurantEmptyMenuException(self::NAME);
			}

			return new Restaurant(self::NAME, $menu);
		} catch (RestaurantEmptyMenuException $e) {
			throw $e;
		} catch (Throwable $e) {
			throw new RestaurantLoadException(self::NAME, $e);
		}
	}

}
