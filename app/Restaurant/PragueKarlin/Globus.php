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

final class Globus extends HtmlParseRestaurantLoader
{

	private const MENU_URL = 'http://restauraceglobus.cz/poledni-menu/';
	private const NAME = 'Restaurace Globus';

	public function loadRestaurant(): Restaurant
	{
		try {
			$response = $this->httpClient->request('GET', self::MENU_URL);
			$html = $response->getBody()->getContents();

			$matcherPrices = Matcher::single('//div[@id="main-content"]', [
				'priceSoup' => './/h4[1]/strong',
				'priceDish' => './/h4[2]/strong',
			])->fromHtml();

			/** @var string[] $rawDishes */
			$prices = $matcherPrices($html);
			$priceSoup = (int) filter_var($prices['priceSoup'], FILTER_SANITIZE_NUMBER_INT);
			$priceMeal = (int) filter_var($prices['priceDish'], FILTER_SANITIZE_NUMBER_INT);

			$matcherDishes = Matcher::single(sprintf('//div[@id="main-content"]//div[contains(@class, "et_pb_text_%s")]', date('N')), [
				'soup' => './/p',
				'meal' => Matcher::multi('.//ol/li'),
			])->fromHtml();

			/** @var array $rawDishes */
			$rawDishes = $matcherDishes($html);
			$soap = new Dish($rawDishes['soup'], $priceSoup);

			$meals = [];
			foreach ($rawDishes['meal'] as $meal) {
				$meals[] = new Dish($meal, $priceMeal);
			}

			// last one is dessert
			array_pop($meals);

			$menu = Menu::createFromDishes([$soap], $meals);

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
