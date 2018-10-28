<?php declare(strict_types = 1);

namespace LunchCrawler\Provider\Karlin;

use Atrox\Matcher;
use Dogma\Geolocation\Position;
use LunchCrawler\Restaurant\HtmlParseRestaurantLoader;
use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Restaurant\Restaurant;
use LunchCrawler\Restaurant\RestaurantEmptyMenuException;
use LunchCrawler\Restaurant\RestaurantLoadException;
use Throwable;

final class Podolka extends HtmlParseRestaurantLoader
{

	private const MENU_URL = 'http://karlin.restauracepodolka.cz/';
	private const NAME = 'Restaurace Podolka';
	private const LAT = 50.0907614;
	private const LNG = 14.4353673;

	public function loadRestaurant(): Restaurant
	{
		try {
			$response = $this->httpClient->request('GET', self::MENU_URL);
			$html = $response->getBody()->getContents();

			$matcher = Matcher::single('//img[@class="fl-photo-img"]/@src')->fromHtml();

			/** @var string $imageSrc */
			$imageSrc = $matcher($html);

			$menu = Menu::createFromImageUrl($imageSrc);

			if ($menu->isEmpty()) {
				throw new RestaurantEmptyMenuException(self::NAME);
			}

			return new Restaurant(self::NAME, $menu, new Position(self::LAT, self::LNG));
		} catch (RestaurantEmptyMenuException $e) {
			throw $e;
		} catch (Throwable $e) {
			throw new RestaurantLoadException(self::NAME, $e);
		}
	}

}
