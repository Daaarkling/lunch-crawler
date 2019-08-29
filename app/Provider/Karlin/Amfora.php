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
use function is_array;
use function preg_replace;
use function preg_split;
use function sprintf;

final class Amfora extends HtmlParseRestaurantLoader
{

	private const MENU_URL = 'http://www.amfora-pizza.cz/';
	private const NAME = 'Pizzeria Amfora';

	public function loadRestaurant(): Restaurant
	{
		try {
			$response = $this->httpClient->request('GET', self::MENU_URL);
			$html = $response->getBody()->getContents();

			$date = sprintf('%s %s', Strings::substring(WeekDay::getCurrentCzechName(), 0, 2), date('d.m.'));
			$matcherDishes = Matcher::multi(sprintf('//div[@id="poledni_menu"]//td[contains(text(), "%s")]/parent::tr/following-sibling::tr[position() <= 3]', $date))->fromHtml();
			$matcherPrice = Matcher::single('//div[@id="poledni_menu"]/preceding-sibling::h2[1]')->fromHtml();

			/** @var mixed[] $days */
			$rawDishes = $matcherDishes($html);
			/** @var mixed[] $days */
			$rawPrice = $matcherPrice($html);

			$price = (int) filter_var($rawPrice, FILTER_SANITIZE_NUMBER_INT);
			if ($price === 0) {
				$price = 119;
			}

			$meals = [];
			foreach ($rawDishes as $rawDish) {
				$dishes = preg_split('~M{0,4}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})\.~', $rawDish, -1, PREG_SPLIT_NO_EMPTY);
				if (!is_array($dishes)) {
					continue;
				}
				foreach ($dishes as $dish) {
					$dish = (string) preg_replace('~\s\s+~', ' ', $dish);
					$meals[] = new Dish($dish, $price);
				}
			}

			$menu = Menu::createFromDishes([], $meals);

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
