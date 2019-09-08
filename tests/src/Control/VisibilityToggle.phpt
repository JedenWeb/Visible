<?php declare(strict_types=1);

/**
 * Test: JedenWeb\Visible\Control\VisibilityToggle.
 *
 * @testCase Tests\Visible\Control\VisibilityToggleTest
 * @author Pavel Jurásek
 * @package JedenWeb\Visible\Control */

namespace Tests\JedenWeb\Visible\Control;

use Doctrine\ORM\EntityManager;
use JedenWeb\TesterUtils\CompiledContainer;
use JedenWeb\TesterUtils\PresenterRunner;
use JedenWeb\Visible\Control\VisibilityToggle;
use Nette;
use Nette\Application\Request;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../src/Entity.php';
require_once __DIR__ . '/../../src/presenters.php';
require_once __DIR__ . '/../../CompiledContainer.php';
require_once __DIR__ . '/../../PresenterRunner.php';

/**
 * @author Pavel Jurásek
 */
class VisibilityToggleTest extends Tester\TestCase
{

	use CompiledContainer {
		createContainer as parentCreateContainer;
	}
	use PresenterRunner;

	protected function createContainer(array $configs = []): Nette\DI\Container
	{
		return $this->parentCreateContainer([
			__DIR__ . '/../config.neon',
		]);
	}

	public function testBasic()
    {
		$this->openPresenter('A:');

		$entityManager = \Mockery::mock(EntityManager::class);
		$entityManager->shouldReceive('flush')
			->once();

		/** @var VisibilityToggle $control */
		$control = $this->presenter['visibilityToggle-1'];

		ob_start();
		$control->render();
		$output = ob_get_clean();

		Assert::matchFile(__DIR__ . '/render/default.html', $output);

		Assert::exception(function () use ($control) {
			$control->handleToggleVisibility();
		}, Nette\Application\AbortException::class);

		$control->setTemplateFile(__DIR__ . '/custom.latte');

		ob_start();
		$control->render();
		$output = ob_get_clean();

		Assert::matchFile(__DIR__ . '/render/custom.html', $output);
    }

	public function testChangeFactoryTemplate()
	{
		$this->openPresenter('C:');

		$entityManager = \Mockery::mock(EntityManager::class);
		$entityManager->shouldReceive('flush')
			->once();

		/** @var VisibilityToggle $control */
		$control = $this->presenter['visibilityToggle-1'];

		ob_start();
		$control->render();
		$output = ob_get_clean();

		Assert::matchFile(__DIR__ . '/render/feather.html', $output);

		Assert::exception(function () use ($control) {
			$control->handleToggleVisibility();
		}, Nette\Application\AbortException::class);
	}

	public function testSecured()
	{
		$this->openPresenter('B:');

		$refl = new \ReflectionProperty(Presenter::class, 'ajaxMode');
		$refl->setAccessible(true);
		$refl->setValue($this->presenter, true);

		Assert::true($this->presenter->isAjax());

		/** @var VisibilityToggle $control */
		$control = $this->presenter['visibilityToggle-1'];

		$link = $control->link('toggleVisibility!');
		preg_match('~_sec=([A-Za-z0-9_-]{8})~', $link, $m);

		$token = $m[1];

		$this->openPresenter('B:');

		$refl = new \ReflectionProperty(Presenter::class, 'ajaxMode');
		$refl->setAccessible(true);
		$refl->setValue($this->presenter, true);

		$refl = new \ReflectionProperty(Control::class, 'params');
		$refl->setAccessible(true);
		$refl->setValue($control, $refl->getValue($control) + [
			'_sec' => $token,
		]);

		$request = new Request('B', 'GET', [
			'action' => 'default',
			'do' => 'visibilityToggle-1-toggleVisibility',
			'visibilityToggle-1-_sec' => $token,
		]);

		$response = $this->presenter->run($request);

		Assert::type(Nette\Application\IResponse::class, $response);
	}

}

(new VisibilityToggleTest())->run();
