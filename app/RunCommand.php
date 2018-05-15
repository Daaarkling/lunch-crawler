<?php declare(strict_types = 1);

namespace LunchCrawler;

use LunchCrawler\Output\OutputHandlerFactory;
use LunchCrawler\Output\OutputOptions;
use LunchCrawler\Restaurant\RestaurantClassLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunCommand extends Command
{

	private const OPTION_OUTPUT = 'output';

	protected function configure(): void
	{
		$this->setName('run')
			->setDescription('Crawl through restaurants.')
			->addOption(
				self::OPTION_OUTPUT,
				'o',
				InputOption::VALUE_REQUIRED,
				'Set output. You can choose from several choices: ' . implode(', ', OutputOptions::OUTPUTS) . '.',
				OutputOptions::CONSOLE
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);

		$outputOption = $input->getOption(self::OPTION_OUTPUT);
		if (!OutputOptions::isValid($outputOption)) {
			$io->error('Output must be one of these options: ' . implode(', ', OutputOptions::OUTPUTS));
			return 1;
		}

		$restaurantClassLoader = new RestaurantClassLoader();
		$restaurants = $restaurantClassLoader->load();

		$progressBar = new ProgressBar($output, count($restaurants));

		$crawler = new Crawler($progressBar);
		$result = $crawler->crawl($restaurants);

		if (!$result->isEmpty()) {
			$outputHandlerFactory = new OutputHandlerFactory();
			$outputHandler = $outputHandlerFactory->create($outputOption, $io);
			$outputHandler->handle($result);
		}

		$message = sprintf(
			'Total: %d, Success: %d, Error: %d',
			$result->getTotalAmount(),
			$result->getNumberOfSuccessful(),
			$result->getNumberOfFailed()
		);

		if ($result->hasErrors()) {
			$io->error($message);
		} else {
			$io->success($message);
		}

		return 0;
	}

}
