<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use Dogma\Geolocation\Position;
use LunchCrawler\Distance\Distance;
use LunchCrawler\Restaurant\Menu\Menu;

class Restaurant
{

	/** @var string */
	private $name;

	/** @var \LunchCrawler\Restaurant\Menu\Menu */
	private $menu;

	/** @var \Dogma\Geolocation\Position */
	private $position;

	/** @var \LunchCrawler\Distance\Distance|null */
	private $distance;

	public function __construct(
		string $name,
		Menu $menu,
		Position $position,
		?Distance $distance = null
	)
	{
		$this->name = $name;
		$this->menu = $menu;
		$this->position = $position;
		$this->distance = $distance;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getMenu(): Menu
	{
		return $this->menu;
	}

	public function getPosition(): Position
	{
		return $this->position;
	}

	public function setDistance(Distance $distance): void
	{
		$this->distance = $distance;
	}

	public function getDistance(): ?Distance
	{
		return $this->distance;
	}

}
