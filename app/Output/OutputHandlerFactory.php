<?php declare(strict_types = 1);

namespace LunchCrawler\Output;

use Maknz\Slack\Client;
use Symfony\Component\Console\Style\SymfonyStyle;

class OutputHandlerFactory
{

	/** @var \Maknz\Slack\Client */
	private $slackClient;

	public function __construct(Client $slackClient)
	{
		$this->slackClient = $slackClient;
	}

	public function create(string $option, SymfonyStyle $io): OutputHandler
	{
		if ($option === OutputOptions::SLACK) {
			$outputHandler = new SlackOutputHandler($this->slackClient);

		} elseif ($option === OutputOptions::CONSOLE) {
			$outputHandler = new ConsoleOutputHandler($io);

		} else {
			$outputHandler = new DumpOutputHandler();
		}

		return $outputHandler;
	}

}
