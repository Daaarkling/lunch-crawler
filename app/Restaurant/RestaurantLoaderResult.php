<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use function count;

class RestaurantLoaderResult
{

	/** @var \LunchCrawler\Restaurant\Restaurant[] */
	private $successful;

	/** @var string[] */
	private $failed;

	/**
	 * @param \LunchCrawler\Restaurant\Restaurant[] $successful
	 * @param string[] $failed
	 */
	public function __construct(array $successful, array $failed)
	{
		$this->successful = $successful;
		$this->failed = $failed;
	}

	public function getTotalCount(): int
	{
		return count($this->failed) + count($this->successful);
	}

	/**
	 * @return \LunchCrawler\Restaurant\Restaurant[]
	 */
	public function getSuccessful(): array
	{
		return $this->successful;
	}

	public function hasSuccessful(): bool
	{
		return $this->getNumberOfSuccessful() > 0;
	}

	public function getNumberOfSuccessful(): int
	{
		return count($this->successful);
	}

	/**
	 * @return string[]
	 */
	public function getFailed(): array
	{
		return $this->failed;
	}

	public function hasFailed(): bool
	{
		return $this->getNumberOfFailed() > 0;
	}

	public function getNumberOfFailed(): int
	{
		return count($this->failed);
	}

}
