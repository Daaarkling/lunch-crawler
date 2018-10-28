<?php declare(strict_types = 1);

namespace LunchCrawler\Output;

class OutputHandlerFactory
{

	/** @var \LunchCrawler\Output\SlackOutputHandler */
	private $slackOutputHandler;

	/** @var \LunchCrawler\Output\ConsoleOutputHandler */
	private $consoleOutputHandler;

	public function __construct(
		ConsoleOutputHandler $consoleOutputHandler,
		SlackOutputHandler $slackOutputHandler
	)
	{
		$this->slackOutputHandler = $slackOutputHandler;
		$this->consoleOutputHandler = $consoleOutputHandler;
	}

	public function create(OutputOption $outputOption): OutputHandler
	{
		if ($outputOption->equals(OutputOption::SLACK)) {
			return $this->slackOutputHandler;
		}

		if ($outputOption->equals(OutputOption::CONSOLE)) {
			return $this->consoleOutputHandler;
		}

		throw new OutputOptionNotSupportedException($outputOption);
	}

}
