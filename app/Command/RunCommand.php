<?php declare(strict_types = 1);

namespace LunchCrawler\Command;

use LunchCrawler\Crawler;
use LunchCrawler\Output\OutputHandlerFactory;
use LunchCrawler\Output\OutputOptions;
use LunchCrawler\Restaurant\RestaurantLoaderCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunCommand extends Command
{

	private const NAME = 'run';
	private const OPTION_OUTPUT = 'output';

	/** @var \LunchCrawler\Crawler */
	private $crawler;

	/** @var \LunchCrawler\Restaurant\RestaurantLoaderCollection */
	private $restaurantLoaderCollection;

	/** @var \LunchCrawler\Output\OutputHandlerFactory */
	private $outputHandlerFactory;

	public function __construct(
		Crawler $crawler,
		RestaurantLoaderCollection $restaurantLoaderCollection,
		OutputHandlerFactory $outputHandlerFactory
	)
	{
		parent::__construct();
		$this->crawler = $crawler;
		$this->restaurantLoaderCollection = $restaurantLoaderCollection;
		$this->outputHandlerFactory = $outputHandlerFactory;
	}


	protected function configure(): void
	{
		$this->setName(self::NAME)
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
			return 2;
		}

		$progressBar = new ProgressBar($output, $this->restaurantLoaderCollection->getCount());

		$this->crawler->setProgressBar($progressBar);
		$result = $this->crawler->crawl($this->restaurantLoaderCollection->getRestaurants());

		if (!$result->isEmpty()) {
			$outputHandler = $this->outputHandlerFactory->create($outputOption, $io);
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
			return 1;
		} else {
			$io->success($message);
			return 0;
		}
	}

}
