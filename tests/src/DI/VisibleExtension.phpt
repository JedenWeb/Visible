<?php

/**
 * Test: JedenWeb\Visible\DI\VisibleExtension.
 *
 * @testCase Tests\Visible\DI\VisibleExtensionTest
 * @author Pavel Jurásek
 * @package JedenWeb\Visible\DI */

namespace Tests\JedenWeb\Visible\DI;

use JedenWeb\Visible\Control\IVisibilityToggle;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @author Pavel Jurásek
 */
class VisibleExtensionTest extends Tester\TestCase
{

    public function setUp()
    {

    }

    public function testBasic()
    {
		$configurator = new Nette\Configurator;
		$configurator->setTempDirectory(TEMP_DIR);

		$configurator->addConfig(__DIR__ . '/../config.neon');

		/** @var Nette\DI\Container $container */
		$container = null;

		Assert::noError(function () use (&$container, $configurator) {
			$container = $configurator->createContainer();
		});

		Assert::true($container->hasService('visible.toggle'));

		Assert::type(IVisibilityToggle::class, $container->getByType(IVisibilityToggle::class));
    }

}

(new VisibleExtensionTest())->run();
