<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant\PragueKarlin;

use Atrox\Matcher;
use LunchCrawler\Restaurant\HtmlParseRestaurant;
use LunchCrawler\Restaurant\Menu\Dish;
use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Restaurant\RestaurantParseException;
use Throwable;

final class Globus extends HtmlParseRestaurant
{

	private const MENU_URL = 'http://restauraceglobus.cz/poledni-menu/';
	private const NAME = 'Restaurace Globus';

	public function loadMenu(): Menu
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

			$matcherDishes = Matcher::single('//div[@id="main-content"]//div[contains(@class, "et_pb_text_2")]', [
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

			return Menu::createFromDishes(self::NAME, [$soap], $meals);

		} catch (Throwable $e) {
			throw new RestaurantParseException(self::NAME, $e);
		}
	}

}
