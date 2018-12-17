<?php declare(strict_types = 1);

namespace LunchCrawler\Command;

use Darkling\ZomatoClient\Request\Enum\EntityType;
use Darkling\ZomatoClient\Request\Restaurant\SearchRequest;
use Darkling\ZomatoClient\Response\ResponseOption;
use Darkling\ZomatoClient\ZomatoClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function is_numeric;
use function is_string;
use function sprintf;

class ZomatoRestaurantIdCommand extends Command
{

	private const NAME = 'zomato:search';
	private const OPTION_CITY = 'city';
	private const ARGUMENT_SEARCH = 'search';
	private const PRAGUE_CITY_ID = 84;

	/** @var \Darkling\ZomatoClient\ZomatoClient */
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
			->setDescription(sprintf('Finds zomato restaurant id in city by given name. If no city id is provided default option is Prague %d.', self::PRAGUE_CITY_ID))
			->addArgument(
				self::ARGUMENT_SEARCH,
				InputArgument::REQUIRED,
				'Name of restaurant'
			)
			->addOption(
				self::OPTION_CITY,
				'c',
				InputOption::VALUE_REQUIRED,
				sprintf('Set city id. Default option is Prague %d.', self::PRAGUE_CITY_ID),
				(string) self::PRAGUE_CITY_ID
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);

		$cityOption = $input->getOption(self::OPTION_CITY);
		if (!is_numeric($cityOption)) {
			$io->error('"--city" option must be numeric.');

			return 2;
		}
		$cityOption = (int) $cityOption;

		$search = $input->getArgument(self::ARGUMENT_SEARCH);
		if (!is_string($search)) {
			$io->error('Invalid "search" argument.');

			return 2;
		}

		$searchRequest = SearchRequest::createFromParameters([
			'entity_id' => $cityOption,
			'entity_type' => EntityType::get(EntityType::CITY),
			'q' => $search,
			'count' => 5,
		]);

		$response = $this->zomatoClient->send($searchRequest, ResponseOption::get(ResponseOption::JSON_STD_CLASS));

		if (!$response->isOk()) {
			$io->error($response->getReasonPhrase());
			return 3;
		}

		$headers = [
			'id',
			'name',
			'url',
		];

		$rows = [];
		foreach ($response->getData()->restaurants as $restaurant) {
			$rows[] = [
				$restaurant->restaurant->id,
				$restaurant->restaurant->name,
				$restaurant->restaurant->url,
			];
		}

		$io->table($headers, $rows);

		return 0;
	}

}
