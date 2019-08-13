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
use function count;
use function date;
use function filter_var;
use function ltrim;
use function strpos;

final class Salanda extends HtmlParseRestaurantLoader
{

	private const MENU_URL = 'https://www.restauracesalanda.cz/cs/salanda/karlin/';
	private const NAME = 'Šalanda';

	private const SOAP_LIMIT = 35;
	private const MEAL_LIMIT = 70;

	public function loadRestaurant(): Restaurant
	{
		try {
			$response = $this->httpClient->request('GET', self::MENU_URL);
			$html = $response->getBody()->getContents();

			$matcher = Matcher::multi('//section[@id="daily-meals"]//a[contains(@class, "js-bookmark-switcher")]', [
				'id' => '@href',
				'text' => 'text()',
			])->fromHtml();

			/** @var string[][] $rawDishes */
			$rawDays = $matcher($html);

			$today = date('d. m.');

			$matchingDayId = null;
			foreach ($rawDays as $rawDay) {
				if (strpos($rawDay['text'], $today)) {
					$matchingDayId = $rawDay['id'];
					break;
				}
			}

			if ($matchingDayId === null) {
				throw new RestaurantEmptyMenuException(self::NAME);
			}

			$matcher = Matcher::multi(sprintf('//table[@id="%s"]//tr', ltrim($matchingDayId, '#')), [
				'name' => './/td[@class="name"]',
				'price' => './/td[contains(@class, "price")]',
			])->fromHtml();

			/** @var string[][] $rawDishes */
			$rawDishes = $matcher($html);

			$meals = [];
			$soaps = [];
			foreach ($rawDishes as $rawDish) {
				$name = $this->restaurantFormatter->sanitizeName($rawDish['name']);
				$name = trim((string) preg_replace('/\s\s+/', ' ', $name));
				$price = $this->extractPrice($rawDish['price']);

				if ($price > self::MEAL_LIMIT) {
					$meals[] = new Dish($name, $price);
				} elseif ($price > self::SOAP_LIMIT && count($meals) === 0) {
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
