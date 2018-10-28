<?php declare(strict_types = 1);

namespace LunchCrawler\Command;

use DateTimeImmutable;
use Dogma\Geolocation\Position;
use LunchCrawler\Crawler\CrawlerBuilder;
use LunchCrawler\Date\Calendar;
use LunchCrawler\Distance\Calculator\StraightLineDistanceCalculator;
use LunchCrawler\Distance\DistanceFacade;
use LunchCrawler\Output\OutputHandlerFactory;
use LunchCrawler\Output\OutputOption;
use LunchCrawler\Restaurant\RestaurantLoaderCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function implode;
use function sprintf;

class RunCommand extends Command
{

	private const NAME = 'run';

	private const OPTION_OUTPUT = 'output';
	private const OPTION_CALENDAR = 'calendar';
	private const OPTION_DISTANCE = 'distance';

	/** @var \Dogma\Geolocation\Position */
	private $startPosition;

	/** @var \LunchCrawler\Distance\DistanceFacade */
	private $distanceFacade;

	/** @var \LunchCrawler\Restaurant\RestaurantLoaderCollection */
	private $restaurantLoaderCollection;

	/** @var \LunchCrawler\Output\OutputHandlerFactory */
	private $outputHandlerFactory;

	/** @var \LunchCrawler\Date\Calendar */
	private $calendar;

	/** @var \LunchCrawler\Distance\Calculator\StraightLineDistanceCalculator */
	private $straightLineDistanceCalculator;

	public function __construct(
		Position $startPosition,
		DistanceFacade $distanceFacade,
		RestaurantLoaderCollection $restaurantLoaderCollection,
		OutputHandlerFactory $outputHandlerFactory,
		Calendar $calendar,
		StraightLineDistanceCalculator $straightLineDistanceCalculator
	)
	{
		parent::__construct();
		$this->startPosition = $startPosition;
		$this->distanceFacade = $distanceFacade;
		$this->restaurantLoaderCollection = $restaurantLoaderCollection;
		$this->outputHandlerFactory = $outputHandlerFactory;
		$this->calendar = $calendar;
		$this->straightLineDistanceCalculator = $straightLineDistanceCalculator;
	}


	protected function configure(): void
	{
		$this->setName(self::NAME)
			->setDescription('Crawl through restaurants.')
			->addOption(
				self::OPTION_OUTPUT,
				'o',
				InputOption::VALUE_REQUIRED,
				'Set output. You can choose from several choices: ' . implode(', ', OutputOption::getAllowedValues()) . '.',
				OutputOption::CONSOLE
			)->addOption(
				self::OPTION_CALENDAR,
				'c',
				InputOption::VALUE_NONE,
				'Crawl only during work days and also skip czech holidays.'
			)->addOption(
				self::OPTION_DISTANCE,
				'd',
				InputOption::VALUE_NONE,
				'Add distance information to restaurants.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->straightLineDistanceCalculator->calculateDistance(
			new Position(50.504678, 16.008652),
			new Position(50.507906, 16.004988)
		);

		/** @var bool $isCalendar */
		$isCalendar = $input->getOption(self::OPTION_CALENDAR);

		if ($isCalendar && !$this->calendar->isWorkDay(new DateTimeImmutable())) {
			return 3;
		}

		$io = new SymfonyStyle($input, $output);

		/** @var string $outputOption */
		$outputOption = $input->getOption(self::OPTION_OUTPUT);

		if (!OutputOption::isValid($outputOption)) {
			$io->error('Output must be one of these options: ' . implode(', ', OutputOption::getAllowedValues()));
			return 2;
		}
		$outputOption = OutputOption::get($outputOption);

		/** @var bool $enableDistance */
		$enableDistance = $input->getOption(self::OPTION_DISTANCE);

		$progressBar = new ProgressBar($output, $this->restaurantLoaderCollection->getCount());

		$crawlerBuilder = new CrawlerBuilder();
		$crawlerBuilder->setProgressBar($progressBar);
		if ($enableDistance) {
			$crawlerBuilder->enableDistance($this->startPosition, $this->distanceFacade);
		}
		$crawler = $crawlerBuilder->build();
		$result = $crawler->crawl($this->restaurantLoaderCollection->getRestaurantLoaders());

		if ($result->hasSuccessful()) {
			$outputHandler = $this->outputHandlerFactory->create($outputOption);
			$outputHandler->handle($result);
		}

		$message = sprintf(
			'Total: %d, Successful: %d, Failed: %d',
			$result->getTotalCount(),
			$result->getNumberOfSuccessful(),
			$result->getNumberOfFailed()
		);

		if ($result->hasFailed()) {
			$io->error($message);
			$io->writeln('Failed:');
			$io->listing($result->getFailed());
			return 1;
		}

		$io->success($message);
		return 0;
	}

}
