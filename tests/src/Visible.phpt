<?php declare(strict_types=1);

/**
 * Test: JedenWeb\Visible\Visible.
 *
 * @testCase Tests\Visible\VisibleTest
 * @author Pavel Jurásek
 * @package JedenWeb\Visible */

namespace Tests\JedenWeb\Visible;

use JedenWeb\Visible\IVisible;
use JedenWeb\Visible\TVisible;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class Entity implements IVisible
{

	use TVisible;

	public function getId()
	{
		return 1;
	}

}

/**
 * @author Pavel Jurásek
 */
class VisibleTest extends Tester\TestCase
{

    public function testBasic()
    {
		$entity = new Entity;

		Assert::true($entity->isVisible());

		$entity->setVisible(false);

		Assert::false($entity->isVisible());

		$entity->toggleVisibility();

		Assert::true($entity->isVisible());
    }

}

(new VisibleTest())->run();
