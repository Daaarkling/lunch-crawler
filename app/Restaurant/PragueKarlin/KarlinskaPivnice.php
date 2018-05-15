<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant\PragueKarlin;

use Atrox\Matcher;
use LunchCrawler\Restaurant\HtmlParseRestaurant;
use LunchCrawler\Restaurant\Menu\Dish;
use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Restaurant\RestaurantParseException;
use Throwable;

final class KarlinskaPivnice extends HtmlParseRestaurant
{

	private const SOAP_LIMIT_PRICE = 44;
	private const MENU_URL = 'http://www.pivnicekarlin.cz';
	private const NAME = 'Karlínská Pivnice';

	public function loadMenu(): Menu
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
				$name = $rawDish['name'];
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

			return Menu::createFromDishes(self::NAME, $soaps, $meals);

		} catch (Throwable $e) {
			throw new RestaurantParseException(self::NAME, $e);
		}
	}

}
