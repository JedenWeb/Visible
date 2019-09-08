<?php declare(strict_types = 1);

namespace Tests\JedenWeb\VIsible\Control;

use JedenWeb\Visible\IVisible;
use JedenWeb\Visible\TVisibilityTogglePresenter;
use Kdyby\Autowired\AutowireComponentFactories;
use Nette\Application\UI\Presenter;
use Nextras\Application\UI\SecuredLinksPresenterTrait;
use Tests\Entity;

class APresenter extends Presenter
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

class CPresenter extends APresenter
{

	public function __construct()
	{
		parent::__construct();

		$this->visibilityTemplateFile = 'VisibilityToggle.feather.latte';
	}

}
