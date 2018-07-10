<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant\Menu;

class Dish
{

	/** @var string */
	private $name;

	/** @var int */
	private $price;

	public function __construct(string $name, int $price)
	{
		$this->name = $name;
		$this->price = $price;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getPrice(): float
	{
		return $this->price;
	}

}
