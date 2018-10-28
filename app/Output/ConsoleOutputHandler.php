<?php declare(strict_types = 1);

namespace LunchCrawler\Output;

use LunchCrawler\Restaurant\RestaurantLoaderResult;
use Symfony\Component\Console\Style\SymfonyStyle;
use function sprintf;
use function str_repeat;

class ConsoleOutputHandler implements OutputHandler
{

	/** @var \Symfony\Component\Console\Style\SymfonyStyle */
	private $io;

	public function __construct(SymfonyStyle $io)
	{
		$this->io = $io;
	}

	public function handle(RestaurantLoaderResult $result): void
	{
		foreach ($result->getSuccessful() as $restaurant) {
			$this->io->title($restaurant->getName());
			$menu = $restaurant->getMenu();

			if ($menu->hasDishes()) {
				$this->io->section('Polévky');

				foreach ($menu->getSoups() as $soup) {
					$this->io->text(sprintf('- %s - %d Kč', $soup->getName(), $soup->getPrice()));
				}

				$this->io->section('Hlavní jídla');

				foreach ($menu->getMeals() as $meal) {
					$this->io->text(sprintf('- %s - %d Kč', $meal->getName(), $meal->getPrice()));
				}
			} elseif ($menu->hasImageUrl()) {
				$this->io->text($menu->getImageUrl());

			} else {
				$this->io->text($menu->getUrl());
			}

			$this->io->section(str_repeat(' ', 100));
		}
	}

}
