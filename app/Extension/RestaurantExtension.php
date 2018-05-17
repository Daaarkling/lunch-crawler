<?php declare(strict_types = 1);

namespace LunchCrawler\Extension;

use LunchCrawler\Restaurant\RestaurantCollection;
use Nette\DI\CompilerExtension;

class RestaurantExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();

		$restaurantCollection = $builder->addDefinition($this->prefix('crawler'))
			->setFactory(RestaurantCollection::class);

		foreach ($config as $key => $restaurants) {
			$serviceName = $this->prefix((string) $key);
			$builder->addDefinition($serviceName)
				->setFactory($restaurants);

			$restaurantCollection->addSetup('addRestaurant', ['@' . $serviceName]);
		}
	}

}
