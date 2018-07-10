<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use LunchCrawler\Restaurant\Menu\Menu;
use Smalot\PdfParser\Parser;
use Throwable;

abstract class PdfRestaurantLoader implements RestaurantLoader
{

	/** @var \Smalot\PdfParser\Parser */
	private $parser;

	public function __construct(Parser $parser)
	{
		$this->parser = $parser;
	}

	public function loadRestaurant(): Restaurant
	{
		try {
			$pdf = $this->parser->parseFile($this->getUrlMenu());

			$menu = $this->loadMenu($pdf->getText());

			if ($menu->isEmpty()) {
				$menu = Menu::createFromUrl($this->getUrlMenu());
			}

			return new Restaurant($this->getName(), $menu);

		} catch (Throwable $e) {
			throw new RestaurantLoadException($this->getName(), $e);
		}
	}

	abstract public function loadMenu(string $text): Menu;

	abstract public function getName(): string;

	abstract public function getUrlMenu(): string;

}
