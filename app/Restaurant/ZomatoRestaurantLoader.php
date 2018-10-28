<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use Darkling\ZomatoClient\Request\Restaurant\DailyMenuRequest;
use Darkling\ZomatoClient\Response\ResponseOption;
use Darkling\ZomatoClient\ZomatoClient;
use Dogma\Geolocation\Position;
use LunchCrawler\Restaurant\Menu\Dish;
use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Zomato\ZomatoRequestFailedException;
use Throwable;

abstract class ZomatoRestaurantLoader implements RestaurantLoader
{

	/** @var int */
	protected static $soapLimitPrice = 45;

	/** @var \Darkling\ZomatoClient\ZomatoClient */
	protected $zomatoClient;

	public function __construct(ZomatoClient $zomatoClient)
	{
		$this->zomatoClient = $zomatoClient;
	}

	public function loadRestaurant(): Restaurant
	{
		try {
			$dailyMenuRequest = new DailyMenuRequest($this->getRestaurantId());
			$response = $this->zomatoClient->send($dailyMenuRequest, ResponseOption::get(ResponseOption::JSON_STD_CLASS));

			if (!$response->isOk()) {
				throw new ZomatoRequestFailedException($dailyMenuRequest, $response);
			}

			$soaps = [];
			$meals = [];

			foreach ($response->getData()->daily_menus as $dailyMenu) {
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

			return new Restaurant($this->getName(), $menu, $this->getPosition());
		} catch (RestaurantEmptyMenuException $e) {
			throw $e;
		} catch (Throwable $e) {
			throw new RestaurantLoadException($this->getName(), $e);
		}
	}

	abstract public function getRestaurantId(): int;

	abstract public function getName(): string;

	abstract public function getPosition(): Position;

	public function getUrlMenu(): string
	{
		return '';
	}

}
