<?php declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Tester\Environment::setup();

define('TEMP_DIR', __DIR__ . '/tmp/test_'. getmypid());

Tester\Helpers::purge(TEMP_DIR);
