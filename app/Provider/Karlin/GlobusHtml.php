<?php declare(strict_types = 1);

namespace LunchCrawler\Provider\Karlin;

use Atrox\Matcher;
use LunchCrawler\Date\WeekDay;
use LunchCrawler\Restaurant\HtmlParseRestaurantLoader;
use LunchCrawler\Restaurant\Menu\Dish;
use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Restaurant\Restaurant;
use LunchCrawler\Restaurant\RestaurantEmptyMenuException;
use LunchCrawler\Restaurant\RestaurantLoadException;
use Nette\Utils\Strings;
use Throwable;
use const FILTER_SANITIZE_NUMBER_INT;
use function date;
use function filter_var;
use function is_int;
use function str_replace;
use function substr;
use function utf8_decode;

final class GlobusHtml extends HtmlParseRestaurantLoader
{

	private const MENU_URL = 'http://restauraceglobus.cz/poledni-menu/';
	private const NAME = 'Restaurace Globus';

	private const SOAP_LIMIT = 30;
	private const MEAL_LIMIT = 80;

	public function loadRestaurant(): Restaurant
	{
		try {
			$response = $this->httpClient->request('GET', self::MENU_URL);
			$html = $response->getBody()->getContents();

			$matcherPartOne = Matcher::multi('//div[@id="primary"]//div[contains(@class, "entry-content")]/p')->fromHtml();
			$matcherPartTwo = Matcher::multi('//div[@id="primary"]//div[contains(@class, "entry-content")]/ol', [
				'dishes' => Matcher::multi('./li'),
			])->fromHtml();

			/** @var string[] $rawDishes */
			$rawDishesMultiPartOne = $matcherPartOne($html);
			$rawDishesMultiPartTwo = $matcherPartTwo($html);

			$today = WeekDay::getCurrentCzechName();
			$tomorrow = WeekDay::getTomorrowCzechName();

			$meals = [];
			$soaps = [];
			$use = false;
			foreach ($rawDishesMultiPartOne as $rawDish) {
				[$name, $price] = $this->extractNameAndPrice($rawDish);

				if ($name === $today) {
					$use = true;
				}

				if ($use && is_int($price)) {
					if ($price > self::MEAL_LIMIT) {
						$meals[] = new Dish($name, $price);
					} elseif ($price > self::SOAP_LIMIT) {
						$soaps[] = new Dish($name, $price);
					}
				}

				if ($name === $tomorrow) {
					break;
				}
			}

			foreach ($rawDishesMultiPartTwo[(int) date('N') - 1]['dishes'] as $rawDish) {
				[$name, $price] = $this->extractNameAndPrice($rawDish);
				$meals[] = new Dish($name, $price);
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

	/**
	 * @param string $value
	 * @return mixed[]
	 */
	private function extractNameAndPrice(string $value): array
	{
		$decodedValue = utf8_decode($value);
		$decodedValue = Strings::trim(str_replace('Polévka', '', $decodedValue), ' ,-–');

		$price = (int) filter_var(substr($decodedValue, -5), FILTER_SANITIZE_NUMBER_INT);

		$name = Strings::trim(str_replace((string) $price, '', $decodedValue));

		return [$name, $price];
	}

}
