<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant\PragueKarlin;

use Atrox\Matcher;
use LunchCrawler\Restaurant\HtmlParseRestaurantLoader;
use LunchCrawler\Restaurant\Menu\Dish;
use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Restaurant\Restaurant;
use LunchCrawler\Restaurant\RestaurantEmptyMenuException;
use LunchCrawler\Restaurant\RestaurantLoadException;
use Throwable;

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
					'soap' => './/p',
					'dish' => Matcher::multi('.//ol/li'),
				]
			)->fromHtml();

			/** @var string[]&string[][] $rawDishes */
			$rawDishes = $matcher($html);

			$soaps[] = new Dish(utf8_decode($rawDishes['soap']), 0);
			$meals = [];
			foreach ($rawDishes['dish'] as $rawDish) {
				$meals[] = new Dish(utf8_decode($rawDish), 0);
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
