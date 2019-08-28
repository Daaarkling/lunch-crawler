<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant\Menu;

class Menu
{

	/** @var \LunchCrawler\Restaurant\Menu\Dish[] */
	private $soups;

	/** @var \LunchCrawler\Restaurant\Menu\Dish[] */
	private $meals;

	/** @var string|null */
	private $imageUrl;

	/** @var string|null */
	private $url;

	/**
	 * @param \LunchCrawler\Restaurant\Menu\Dish[] $soups
	 * @param \LunchCrawler\Restaurant\Menu\Dish[] $meals
	 * @param string|null $imageUrl
	 * @param string|null $url
	 */
	private function __construct(
		array $soups = [],
		array $meals = [],
		?string $imageUrl = null,
		?string $url = null
	)
	{
		$this->soups = $soups;
		$this->meals = $meals;
		$this->imageUrl = $imageUrl;
		$this->url = $url;
	}

	/**
	 * @param \LunchCrawler\Restaurant\Menu\Dish[] $soups
	 * @param \LunchCrawler\Restaurant\Menu\Dish[] $meals
	 * @return \LunchCrawler\Restaurant\Menu\Menu
	 */
	public static function createFromDishes(array $soups = [], array $meals = []): Menu
	{
		return new self($soups, $meals);
	}

	public static function createFromImageUrl(string $imageUrl): Menu
	{
		return new self([], [], $imageUrl);
	}

	public static function createFromUrl(string $url): Menu
	{
		return new self([], [], null, $url);
	}

	public function hasDishes(): bool
	{
		return $this->soups !== [] || $this->meals !== [];
	}

	public function hasSoaps(): bool
	{
		return $this->soups !== [];
	}

	public function hasImageUrl(): bool
	{
		return $this->imageUrl !== null;
	}

	public function hasUrl(): bool
	{
		return $this->url !== null;
	}

	public function isEmpty(): bool
	{
		return !$this->hasDishes() && !$this->hasImageUrl() && !$this->hasUrl();
	}

	/**
	 * @return \LunchCrawler\Restaurant\Menu\Dish[]
	 */
	public function getSoups(): array
	{
		return $this->soups;
	}

	/**
	 * @return \LunchCrawler\Restaurant\Menu\Dish[]
	 */
	public function getMeals(): array
	{
		return $this->meals;
	}

	public function getImageUrl(): ?string
	{
		return $this->imageUrl;
	}

	public function getUrl(): ?string
	{
		return $this->url;
	}

}
