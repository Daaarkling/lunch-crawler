<?php declare(strict_types = 1);

namespace LunchCrawler\Output;

use LunchCrawler\Result;
use Maknz\Slack\Client;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class SlackOutputHandler implements OutputHandler
{

	/** @var \Symfony\Component\Console\Style\SymfonyStyle */
	private $io;

	/** @var \Symfony\Component\Console\Output\BufferedOutput */
	private $output;

	/** @var \Maknz\Slack\Client */
	private $client;

	public function __construct(Client $client)
	{
		$this->client = $client;
		$this->output = new BufferedOutput();
		$this->io = new SymfonyStyle(new StringInput(''), $this->output);
	}

	public function handle(Result $result): void
	{
		foreach ($result->getMenu() as $menu) {
			$this->io->title(sprintf('*%s*', $menu->getName()));

			if ($menu->hasDishes()) {
				$this->io->section('*Polévky*');
				foreach ($menu->getSoups() as $soup) {
					$this->io->text(sprintf('- %s - %d Kč', $soup->getName(), $soup->getPrice()));
				}

				$this->io->section('*Hlavní jídla*');
				foreach ($menu->getMeals() as $meal) {
					$this->io->text(sprintf('- %s - %d Kč', $meal->getName(), $meal->getPrice()));
				}
			} elseif ($menu->hasImageUrl()) {
				$this->io->text(sprintf('![menu](%s)', $menu->getImageUrl()));
			} else {
				$this->io->text($menu->getUrl());
			}

			$this->io->section(str_repeat(' ', 100));
		}

		$this->client->send($this->output->fetch());
	}

}
