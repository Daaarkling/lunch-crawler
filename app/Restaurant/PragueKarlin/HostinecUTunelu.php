<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant\PragueKarlin;

use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Restaurant\Restaurant;

final class HostinecUTunelu implements Restaurant
{

	private const MENU_URL = 'http://www.utunelu.cz/denni_menu.pdf';
	private const NAME = 'Hostinec U Tunelu';

	public function loadMenu(): Menu
	{
		return Menu::createFromUrl(self::NAME, self::MENU_URL);
	}

}
