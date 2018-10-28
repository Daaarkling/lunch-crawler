<?php declare(strict_types = 1);

namespace LunchCrawler\Output\Formatter;

use LunchCrawler\Restaurant\RestaurantLoaderResult;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class SymfonyIOResultFormatter implements StringResultFormatter
{

	public function formatResultIntoString(RestaurantLoaderResult $result): string
	{
		if ($result->isEmpty()) {
			return '';
		}

		$output = new BufferedOutput();
		$io = new SymfonyStyle(new StringInput(''), $output);

		foreach ($result->getSuccessful() as $restaurant) {
			$io->title(sprintf('*%s*', $restaurant->getName()));
			$menu = $restaurant->getMenu();

			if ($menu->hasDishes()) {
				$io->section('*Polévky*');

				foreach ($menu->getSoups() as $soup) {
					$io->text(sprintf('- %s - %d Kč', $soup->getName(), $soup->getPrice()));
				}

				$io->section('*Hlavní jídla*');

				foreach ($menu->getMeals() as $meal) {
					$io->text(sprintf('- %s - %d Kč', $meal->getName(), $meal->getPrice()));
				}
			} elseif ($menu->hasImageUrl()) {
				$io->text($menu->getImageUrl());

			} else {
				$io->text($menu->getUrl());
			}

			$io->section(str_repeat(' ', 100));
		}

		return $output->fetch();
	}

}
