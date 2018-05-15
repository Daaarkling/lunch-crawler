<?php declare(strict_types = 1);

namespace LunchCrawler\Restaurant;

use Nette\Loaders\RobotLoader;
use ReflectionClass;

class RestaurantClassLoader
{

	/**
	 * @return \LunchCrawler\Restaurant\Restaurant[]
	 */
	public function load(): array
	{
		$loader = new RobotLoader();
		$loader->addDirectory(__DIR__ . '/../Restaurant')
			->setTempDirectory(__DIR__ . '/../../temp')
			->register();

		/** @var string[] $classes */
		$classes = array_filter(array_keys($loader->getIndexedClasses()), function ($className): bool {
				$reflectionClass = new ReflectionClass($className);
				return $reflectionClass->implementsInterface(Restaurant::class) && $reflectionClass->isInstantiable();
		});

		$objects = [];
		foreach ($classes as $class) {
			$objects[] = new $class();
		}

		return $objects;
	}

}
