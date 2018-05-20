<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use LunchCrawler\Restaurant\Menu\Menu;

class Restaurant
{

	/** @var string */
	private $name;

	/** @var \LunchCrawler\Restaurant\Menu\Menu */
	private $menu;

	public function __construct(
		string $name,
		Menu $menu
	)
	{
		$this->name = $name;
		$this->menu = $menu;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getMenu(): Menu
	{
		return $this->menu;
	}

}
