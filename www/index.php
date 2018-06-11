<?php declare(strict_types = 1);

use LunchCrawler\Command\RunCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/../app/bootstrap.php';

$params = $container->getParameters();

if (!array_key_exists('wwwSecret', $params)) {
	echo 'Define `wwwSecret` in neon parameters section.';
	exit(1);
}

/** @var \Nette\Http\IRequest $request */
$request = $container->getByType(\Nette\Http\IRequest::class);

if ($request->getQuery('secret') !== $params['wwwSecret']) {
	echo 'Secret token is not valid.';
	exit(1);
}

/** @var \LunchCrawler\Command\RunCommand $runCommand */
$runCommand = $container->getByType(RunCommand::class);

$output = new NullOutput();
$input = new ArrayInput([
	'--output' => 'slack'
]);

$statusCode = $runCommand->run($input, $output);
echo $statusCode;
