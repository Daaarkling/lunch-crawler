<?php declare(strict_types = 1);

namespace LunchCrawler;

class Result
{

	/** @var \LunchCrawler\Restaurant\Menu\Menu[] */
	private $menu;

	/** @var int */
	private $totalAmount;

	/**
	 * @param \LunchCrawler\Restaurant\Menu\Menu[] $menu
	 * @param int $totalAmount
	 */
	public function __construct(array $menu, int $totalAmount)
	{
		$this->menu = $menu;
		$this->totalAmount = $totalAmount;
	}

	public function getNumberOfFailed(): int
	{
		return $this->totalAmount - count($this->menu);
	}

	/**
	 * @return \LunchCrawler\Restaurant\Menu\Menu[]
	 */
	public function getMenu(): array
	{
		return $this->menu;
	}

	public function getTotalAmount(): int
	{
		return $this->totalAmount;
	}

	public function getNumberOfSuccessful(): int
	{
		return count($this->menu);
	}

	public function hasErrors(): bool
	{
		return $this->getNumberOfFailed() > 0;
	}

	public function isEmpty(): bool
	{
		return $this->getNumberOfSuccessful() === 0;
	}

}
