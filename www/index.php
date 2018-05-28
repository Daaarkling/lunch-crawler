<?php declare(strict_types = 1);

use LunchCrawler\Command\RunCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/../app/bootstrap.php';

/** @var \LunchCrawler\Command\RunCommand $runCommand */
$runCommand = $container->getByType(RunCommand::class);

$output = new NullOutput();
$input = new ArrayInput([
	'--output' => 'slack'
]);

$statusCode = $runCommand->run($input, $output);
echo $statusCode;
