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

	public function create(string $option): OutputHandler
	{
		if (!OutputOptions::isValid($option)) {
			throw new InvalidOutputOptionException($option);
		}

		if ($option === OutputOptions::SLACK) {
			return $this->slackOutputHandler;
		}

		return $this->consoleOutputHandler;
	}

}
