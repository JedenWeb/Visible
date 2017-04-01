<?php

/**
 * Test: JedenWeb\Visible\Control\VisibilityToggle.
 *
 * @testCase Tests\Visible\Control\VisibilityToggleTest
 * @author Pavel Jurásek
 * @package JedenWeb\Visible\Control */

namespace Tests\JedenWeb\Visible\Control;

use JedenWeb\TesterUtils\CompiledContainer;
use JedenWeb\TesterUtils\PresenterRunner;
use JedenWeb\Visible\Control\VisibilityToggle;
use JedenWeb\Visible\IVisible;
use JedenWeb\Visible\TVisibilityTogglePresenter;
use JedenWeb\Visible\TVisible;
use Kdyby\Autowired\AutowireComponentFactories;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Nette\Application\Request;
use Nette\DI\Container;
use Nextras\Application\UI\SecuredLinksPresenterTrait;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

class Entity implements IVisible
{

	use TVisible;

	public function getId()
	{
		return 1;
	}

}

class APresenter extends Nette\Application\UI\Presenter
{

	use AutowireComponentFactories;
	use TVisibilityTogglePresenter;

	private $registry = [];

	protected function getTarget($id): IVisible
	{
		if (!in_array($id, $this->registry)) {
			$this->registry[$id] = new Entity;
		}

		return $this->registry[$id];
	}

	protected function beforeRender()
	{
		$this->terminate();
	}

}

class BPresenter extends APresenter
{

	use SecuredLinksPresenterTrait;

}

/**
 * @author Pavel Jurásek
 */
class VisibilityToggleTest extends Tester\TestCase
{

	use CompiledContainer {
		createContainer as parentCreateContainer;
	}
	use PresenterRunner;

	protected function createContainer(array $configs = []): Container
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

	/***/
	public function testSecured()
	{
		$this->openPresenter('B:');

		$refl = new \ReflectionProperty(Nette\Application\UI\Presenter::class, 'ajaxMode');
		$refl->setAccessible(true);
		$refl->setValue($this->presenter, true);

		Assert::true($this->presenter->isAjax());

		/** @var VisibilityToggle $control */
		$control = $this->presenter['visibilityToggle-1'];

		$link = $control->link('toggleVisibility!');
		preg_match('~_sec=([A-Za-z0-9_-]{8})~', $link, $m);

		$token = $m[1];

		$this->openPresenter('B:');

		$refl = new \ReflectionProperty(Nette\Application\UI\Presenter::class, 'ajaxMode');
		$refl->setAccessible(true);
		$refl->setValue($this->presenter, true);

		$refl = new \ReflectionProperty(Nette\Application\UI\Control::class, 'params');
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

		Assert::null($response); // why?!
	}

	protected function getPresenter($name): Nette\Application\UI\Presenter
	{
		$presenter = $this->getContainer()
			->getByType('Nette\Application\IPresenterFactory')
			->createPresenter($name);

		$presenter->autoCanonicalize = FALSE;

		return $presenter;
	}

}

(new VisibilityToggleTest())->run();
