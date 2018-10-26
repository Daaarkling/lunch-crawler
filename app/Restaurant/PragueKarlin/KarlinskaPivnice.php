<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant\PragueKarlin;

use Atrox\Matcher;
use LunchCrawler\Restaurant\HtmlParseRestaurantLoader;
use LunchCrawler\Restaurant\Menu\Dish;
use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Restaurant\Restaurant;
use LunchCrawler\Restaurant\RestaurantEmptyMenuException;
use LunchCrawler\Restaurant\RestaurantFormatter;
use LunchCrawler\Restaurant\RestaurantLoadException;
use Throwable;

final class KarlinskaPivnice extends HtmlParseRestaurantLoader
{

	private const SOAP_LIMIT_PRICE = 44;
	private const MENU_URL = 'http://www.pivnicekarlin.cz';
	private const NAME = 'Karlínská Pivnice';

	public function loadRestaurant(): Restaurant
	{
		try {
			$response = $this->httpClient->request('GET', self::MENU_URL);
			$html = $response->getBody()->getContents();

			$matcher = Matcher::multi('//div[@id="modal-lunch"]//div[contains(@class, "meal")]', [
				'name' => './/h4',
				'price' => './/div[3]',
			])->fromHtml();

			/** @var string[][] $rawDishes */
			$rawDishes = $matcher($html);

			$soaps = [];
			$meals = [];

			foreach ($rawDishes as $rawDish) {
				$name = RestaurantFormatter::format($rawDish['name']);
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
