<?php declare(strict_types = 1);

namespace LunchCrawler\Output;

use LunchCrawler\Slack\SlackFactory;
use Symfony\Component\Console\Style\SymfonyStyle;

class OutputHandlerFactory
{

	public function create(string $option, SymfonyStyle $io): OutputHandler
	{
		if ($option === OutputOptions::SLACK) {
			$slackIniFile = __DIR__ . '/../Slack/slack.ini';
			$slackFactory = new SlackFactory();
			$outputHandler = new SlackOutputHandler($slackFactory->create($slackIniFile));

		} elseif ($option === OutputOptions::CONSOLE) {
			$outputHandler = new ConsoleOutputHandler($io);

		} else {
			$outputHandler = new DumpOutputHandler();
		}

		return $outputHandler;
	}

}
