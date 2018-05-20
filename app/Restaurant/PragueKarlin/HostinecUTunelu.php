<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant\PragueKarlin;

use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Restaurant\Restaurant;
use LunchCrawler\Restaurant\RestaurantLoader;

final class HostinecUTunelu implements RestaurantLoader
{

	private const MENU_URL = 'http://www.utunelu.cz/denni_menu.pdf';
	private const NAME = 'Hostinec U Tunelu';

	public function loadRestaurant(): Restaurant
	{
		$menu = Menu::createFromUrl(self::MENU_URL);
		return new Restaurant(self::NAME, $menu);
	}

}
