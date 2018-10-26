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
		if (!OutputOptions::isValid($option)) {
			throw new InvalidOutputOptionException($option);
		}

		if ($option === OutputOptions::SLACK) {
			return new SlackOutputHandler($this->slackClient);
		}

		return new ConsoleOutputHandler($io);
	}

}
