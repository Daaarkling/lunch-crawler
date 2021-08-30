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
use Throwable;
use function date;
use function str_replace;
use const FILTER_SANITIZE_NUMBER_INT;
use function explode;
use function filter_var;
use function sprintf;
use function trim;

final class BistroYolo extends HtmlParseRestaurantLoader
{

	private const MENU_URL = 'https://www.bistroyolo.cz/';
	private const NAME = 'Bistro Yolo';

	public function loadRestaurant(): Restaurant
	{
		try {
			$response = $this->httpClient->request('GET', self::MENU_URL);
			$html = $response->getBody()->getContents();

			$startDay = WeekDay::getCurrentCzechName() . date(' j.n.');
			$matcherSoup = Matcher::single(sprintf('//div[contains(@class, "jidelak1")]//div[contains(text(), "%s")]', $startDay))->fromHtml();
			$matcherMeals = Matcher::multi(sprintf('//div[contains(@class, "jidelak1")]//div[contains(text(), "%s")]/following-sibling::div[position() <= 2]', $startDay))->fromHtml();

			/** @var string[] $rawMeals */
			$rawMeals = $matcherMeals($html);
			/** @var string $rawSoup */
			$rawSoup = $matcherSoup($html);

			$soaps = [];
			$meals = [];

			$rawSoup = trim(str_replace($startDay, '', $rawSoup));
			$parts = explode('/', $rawSoup);
			$name = trim($parts[0]);
			$price = $this->extractPrice($parts[1]);
			$soaps[] = new Dish($name, $price);

			foreach ($rawMeals as $rawMeal) {
				$parts = explode('/', $rawMeal);
				$name = trim($parts[0]);
				$price = $this->extractPrice($parts[1]);
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

	private function extractPrice(string $value): int
	{
		return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
	}

}
