<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use LunchCrawler\Restaurant\Menu\Dish;
use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Zomato\ZomatoClient;
use Throwable;

abstract class ZomatoRestaurant implements Restaurant
{

	/** @var int */
	protected static $soapLimitPrice = 45;

	/** @var \LunchCrawler\Zomato\ZomatoClient */
	protected $zomatoClient;

	public function __construct(ZomatoClient $zomatoClient)
	{
		$this->zomatoClient = $zomatoClient;
	}

	public function loadMenu(): Menu
	{
		try {
			$data = $this->zomatoClient->getDailyMenu($this->getRestaurantId());

			$soaps = [];
			$meals = [];
			foreach ($data->daily_menus[0]->daily_menu->dishes as $rawDish) {

				$name = $rawDish->dish->name;
				$price = (int) $rawDish->dish->price;
				if ($name === '' || $price === 0) {
					continue;
				}

				if ($price < self::$soapLimitPrice) {
					$soaps[] = new Dish($name, $price);
				} else {
					$meals[] = new Dish($name, $price);
				}
			}

			return Menu::createFromDishes($this->getName(), $soaps, $meals);

		} catch (Throwable $e) {
			throw new RestaurantParseException($this->getName(), $e);
		}
	}

	abstract public function getRestaurantId(): int;

	abstract public function getName(): string;

}
