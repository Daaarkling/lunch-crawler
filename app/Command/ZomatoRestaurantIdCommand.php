<?php declare(strict_types = 1);

namespace LunchCrawler\Command;

use LunchCrawler\Zomato\ZomatoClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ZomatoRestaurantIdCommand extends Command
{

	private const NAME = 'zomato:search';
	private const OPTION_CITY = 'city';
	private const ARGUMENT_SEARCH = 'search';

	/** @var \LunchCrawler\Zomato\ZomatoClient */
	private $zomatoClient;

	public function __construct(
		ZomatoClient $zomatoClient
	)
	{
		parent::__construct();
		$this->zomatoClient = $zomatoClient;
	}


	protected function configure(): void
	{
		$this->setName(self::NAME)
			->setDescription(sprintf('Finds zomato restaurant id in city by given name. If no city id is provided default option is Prague %d.', ZomatoClient::PRAGUE_CITY_ID))
			->addArgument(
				self::ARGUMENT_SEARCH,
				InputArgument::REQUIRED,
				'Name of restaurant'
			)
			->addOption(
				self::OPTION_CITY,
				'c',
				InputOption::VALUE_REQUIRED,
				sprintf('Set city id. Default option is Prague %d.', ZomatoClient::PRAGUE_CITY_ID),
				(string) ZomatoClient::PRAGUE_CITY_ID
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);

		$cityOption = (int) $input->getOption(self::OPTION_CITY);
		$search = (string) $input->getArgument(self::ARGUMENT_SEARCH);

		if ($cityOption <= 0) {
			$io->error('Invalid city id');

			return 2;
		}

		$possibilities = $this->zomatoClient->getRestaurantId($search, $cityOption);

		$headers = [
			'id',
			'name',
			'url',
		];

		$rows = [];

		foreach ($possibilities as $possibility) {
			$rows[] = [
				$possibility['id'],
				$possibility['name'],
				$possibility['url'],
			];
		}

		$io->table($headers, $rows);

		return 0;
	}

}
