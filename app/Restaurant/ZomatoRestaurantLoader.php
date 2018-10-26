<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use LunchCrawler\Restaurant\Menu\Dish;
use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Zomato\ZomatoClient;
use Throwable;

abstract class ZomatoRestaurantLoader implements RestaurantLoader
{

	/** @var int */
	protected static $soapLimitPrice = 45;

	/** @var \LunchCrawler\Zomato\ZomatoClient */
	protected $zomatoClient;

	public function __construct(ZomatoClient $zomatoClient)
	{
		$this->zomatoClient = $zomatoClient;
	}

	public function loadRestaurant(): Restaurant
	{
		try {
			$data = $this->zomatoClient->getDailyMenu($this->getRestaurantId());

			$soaps = [];
			$meals = [];

			foreach ($data->daily_menus as $dailyMenu) {
				foreach ($dailyMenu->daily_menu->dishes as $rawDish) {

					$name = $rawDish->dish->name;
					$price = (int) $rawDish->dish->price;

					if ($price < self::$soapLimitPrice) {
						$soaps[] = new Dish($name, $price);
					} else {
						$meals[] = new Dish($name, $price);
					}
				}
			}

			$menu = Menu::createFromDishes($soaps, $meals);

			if ($menu->isEmpty() && $this->getUrlMenu() !== '') {
				$menu = Menu::createFromUrl($this->getUrlMenu());

			} elseif ($menu->isEmpty()) {
				throw new RestaurantEmptyMenuException($this->getName());
			}

			return new Restaurant($this->getName(), $menu);
		} catch (RestaurantEmptyMenuException $e) {
			throw $e;
		} catch (Throwable $e) {
			throw new RestaurantLoadException($this->getName(), $e);
		}
	}

	abstract public function getRestaurantId(): int;

	abstract public function getName(): string;

	public function getUrlMenu(): string
	{
		return '';
	}

}
