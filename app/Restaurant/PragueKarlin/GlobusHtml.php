<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant\PragueKarlin;

use Atrox\Matcher;
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
use function str_replace;

final class GlobusHtml extends HtmlParseRestaurantLoader
{

	private const MENU_URL = 'http://restauraceglobus.cz/poledni-menu/';
	private const NAME = 'Restaurace Globus';

	public function loadRestaurant(): Restaurant
	{
		try {
			$response = $this->httpClient->request('GET', self::MENU_URL);
			$html = $response->getBody()->getContents();

			$day = date('N');
			$matcher = Matcher::single(
				sprintf(
					'//div[@id="primary"]//div[contains(@class, "vc_col-sm-6")][%d]//div[contains(@class, "wpb_text_column")][%d]',
					$day <= 3 ? 1 : 2,
					$day <= 3 ? $day : $day - 3
				),
				[
					'soap' => './/p[2]',
					'special1' => './/p[3]',
					'special2' => './/p[4]',
					'dish' => Matcher::multi('.//ol/li'),
				]
			)->fromHtml();

			/** @var string[]&string[][] $rawDishes */
			$rawDishes = $matcher($html);

			[$name, $price] = $this->extractNameAndPrice($rawDishes['soap']);
			$soaps[] = new Dish($name, $price);

			$meals = [];
			foreach ($rawDishes['dish'] as $rawDish) {
				[$name, $price] = $this->extractNameAndPrice($rawDish);
				$meals[] = new Dish($name, $price);
			}

			if ($rawDishes['special1'] !== null && $rawDishes['special1'] !== '') {
				[$name, $price] = $this->extractNameAndPrice($rawDishes['special1']);
				$meals[] = new Dish($name, $price);
			}

			if ($rawDishes['special2'] !== null && $rawDishes['special2'] !== '') {
				[$name, $price] = $this->extractNameAndPrice($rawDishes['special2']);
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
		$price = (int) filter_var(substr($value, -5), FILTER_SANITIZE_NUMBER_INT);
		$name = Strings::trim(utf8_decode(str_replace($price . ',-', '', $value)));

		return [$name, $price];
	}

}
