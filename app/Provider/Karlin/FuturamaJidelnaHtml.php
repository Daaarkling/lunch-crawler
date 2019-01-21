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
use const FILTER_SANITIZE_NUMBER_INT;
use function filter_var;
use function preg_match;

final class FuturamaJidelnaHtml extends HtmlParseRestaurantLoader
{

	private const MENU_URL = 'https://www.prague-catering.cz/provozovny/jidelna-kantyna-praha/jidelna-denni-menu-praha-8/';
	private const NAME = 'Futurama jídelna';

	private const SOAP_LIMIT = 22;
	private const MEAL_LIMIT = 70;

	public function loadRestaurant(): Restaurant
	{
		try {
			$response = $this->httpClient->request('GET', self::MENU_URL);
			$html = $response->getBody()->getContents();

			$matcher = Matcher::multi('//section[@id="hlavni"]//table[@class="dennimenu"]//tr', [
				'name' => './td[1]',
				'price' => './td[2]',
			])->fromHtml();

			/** @var string[][] $rawDishes */
			$rawDishes = $matcher($html);

			$meals = [];
			$soaps = [];
			foreach ($rawDishes as $rawDish) {
				$name = $this->restaurantFormatter->sanitizeName($rawDish['name']);
				$price = $this->extractPrice($rawDish['price']);

				if (preg_match('~Salátový\sbar~', $name)) {
					break;
				}

				if ($price > self::MEAL_LIMIT) {
					$meals[] = new Dish($name, $price);
				} elseif ($price > self::SOAP_LIMIT) {
					$soaps[] = new Dish($name, $price);
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

	private function extractPrice(string $value): int
	{
		return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
	}

}
