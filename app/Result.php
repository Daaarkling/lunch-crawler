<?php declare(strict_types = 1);

namespace LunchCrawler;

class Result
{

	/** @var \LunchCrawler\Restaurant\Restaurant[] */
	private $restaurants;

	/** @var int */
	private $totalAmount;

	/**
	 * @param \LunchCrawler\Restaurant\Restaurant[] $restaurants
	 * @param int $totalAmount
	 */
	public function __construct(array $restaurants, int $totalAmount)
	{
		$this->restaurants = $restaurants;
		$this->totalAmount = $totalAmount;
	}

	/**
	 * @return \LunchCrawler\Restaurant\Restaurant[]
	 */
	public function getRestaurants(): array
	{
		return $this->restaurants;
	}

	public function getNumberOfFailed(): int
	{
		return $this->totalAmount - count($this->restaurants);
	}

	public function getTotalAmount(): int
	{
		return $this->totalAmount;
	}

	public function getNumberOfSuccessful(): int
	{
		return count($this->restaurants);
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
