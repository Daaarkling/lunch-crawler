<?php declare(strict_types = 1);

namespace LunchCrawler\Provider\Karlin;

use Atrox\Matcher;
use DateTimeImmutable;
use LunchCrawler\Date\CzechMonths;
use LunchCrawler\Restaurant\HtmlParseRestaurantLoader;
use LunchCrawler\Restaurant\Menu\Dish;
use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Restaurant\Restaurant;
use LunchCrawler\Restaurant\RestaurantEmptyMenuException;
use LunchCrawler\Restaurant\RestaurantLoadException;
use Nette\Utils\Strings;
use Throwable;
use const FILTER_SANITIZE_NUMBER_INT;
use function filter_var;
use function sprintf;

final class PivoKarlin extends HtmlParseRestaurantLoader
{

	private const SOAP_MAX_PRICE = 48;
	private const SOAP_MIN_PRICE = 40;
	private const MEAL_MIN_PRICE = 100;
	private const MENU_URL = 'http://www.pivokarlin.cz/';
	private const NAME = 'Pivo Karlín';

	public function loadRestaurant(): Restaurant
	{
		try {
			$response = $this->httpClient->request('GET', self::MENU_URL);
			$html = $response->getBody()->getContents();

			$now = new DateTimeImmutable();
			$czechMonth = CzechMonths::getFromDateTime($now);
			$date = sprintf('%s. %s %s', $now->format('j'), Strings::upper($czechMonth->getGenetiv()), $now->format('Y'));

			$matcher = Matcher::multi(sprintf('//div[@id="tab-poledni-nabidka"]//div[contains(@class, "col span_12")]//div[contains(@class, "wpb_text_column wpb_content_element")]//h5[contains(text(), "%s")]/parent::div/parent::div/parent::div/div[contains(@class, "ectar_food_menu_item")]', $date), [
				'name' => './/div[contains(@class, "item_name")]',
				'price' => './/div[contains(@class, "price")]',
			])->fromHtml();

			/** @var string[][]|null[][] $rawDishes */
			$rawDishes = $matcher($html);

			$soaps = [];
			$meals = [];

			foreach ($rawDishes as $rawDish) {
				$name = $rawDish['name'];
				if ($name === null || $name === '') {
					continue;
				}

				$price = $this->extractPrice((string) $rawDish['price']);

				if ($name === '' || $price === 0) {
					continue;
				}

				if ($price >= self::SOAP_MIN_PRICE && $price <= self::SOAP_MAX_PRICE) {
					$soaps[] = new Dish($name, $price);

				} elseif ($price >= self::MEAL_MIN_PRICE) {
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

	private function extractPrice(string $value): int
	{
		return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
	}

}
