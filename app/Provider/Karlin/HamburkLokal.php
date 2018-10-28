<?php declare(strict_types = 1);

namespace LunchCrawler\Provider\Karlin;

use Atrox\Matcher;
use LunchCrawler\Restaurant\HtmlParseRestaurantLoader;
use LunchCrawler\Restaurant\Menu\Dish;
use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Restaurant\Restaurant;
use LunchCrawler\Restaurant\RestaurantEmptyMenuException;
use LunchCrawler\Restaurant\RestaurantLoadException;
use Throwable;

final class HamburkLokal extends HtmlParseRestaurantLoader
{

	private const SOAP_LIMIT_PRICE = 50;
	private const MENU_URL = 'http://lokal-hamburk.ambi.cz/cz/';
	private const NAME = 'Hamburk Lokál';

	public function loadRestaurant(): Restaurant
	{
		try {
			$response = $this->httpClient->request('GET', self::MENU_URL);
			$html = $response->getBody()->getContents();

			$matcher = Matcher::multi('//div[@id="lunch-menu"]/div[@class="header-part"]/span[contains(text(), "dnes")]/parent::div/parent::div//img[contains(@alt, "Hamburk")]/ancestor::div[contains(@class, "boxx")]//tr[not(@class)]', [
				'name' => './td[1]',
				'price' => './td[2]',
			])->fromHtml();

			/** @var string[][] $rawDishes */
			$rawDishes = $matcher($html);

			$soaps = [];
			$meals = [];

			foreach ($rawDishes as $rawDish) {
				$name = $this->restaurantFormatter->sanitizeName($rawDish['name']);
				$price = (int) $rawDish['price'];

				if ($name === '' || $price === 0) {
					continue;
				}

				if ($price < self::SOAP_LIMIT_PRICE) {
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
