<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant\PragueKarlin;

use LunchCrawler\Restaurant\Menu\Dish;
use LunchCrawler\Restaurant\Menu\Menu;
use LunchCrawler\Restaurant\PdfRestaurantLoader;
use LunchCrawler\WeekDay;
use Nette\Utils\Strings;

final class HostinecUTuneluPdf extends PdfRestaurantLoader
{

	public function loadMenu(string $text): Menu
	{
		$startDay = mb_strtoupper(WeekDay::getCurrentCzechName());

		if (WeekDay::isFriday()) {
			$endDay = 'NABÍDKA';
		} else {
			$endDay = mb_strtoupper(WeekDay::getTomorrowCzechName());
		}

		$pattern = sprintf('~(?<=%s)(.*)(?=%s)~isU', $startDay, $endDay);
		$textDay = Strings::match($text, $pattern)[0];
		$normalizeTextDay = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($textDay));

		$rows = explode(PHP_EOL, $normalizeTextDay);

		$soaps = [];
		$meals = [];

		foreach ($rows as $key => $row) {
			$priceValues = Strings::match($row, '~(\d+),-~');

			if ($priceValues === null) {
				$previousMeal = $meals[$key - 1];
				$previousMeal->setName(sprintf('%s %s', $previousMeal->getName(), trim($row)));
				continue;
			}

			$price = (int) $priceValues[1];
			$name = trim(str_replace($priceValues[0], '', $row));

			if ($key === 0) {
				$soaps[$key] = new Dish($name, $price);
				continue;
			}

			$meals[$key] = new Dish($name, $price);
		}

		return Menu::createFromDishes($soaps, $meals);
	}

	public function getName(): string
	{
		return 'Hostinec U Tunelu';
	}

	public function getUrlMenu(): string
	{
		return 'http://www.utunelu.cz/denni_menu.pdf';
	}

}
