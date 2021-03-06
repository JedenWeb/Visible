<?php declare(strict_types = 1);

namespace JedenWeb\Visible;

use JedenWeb\Visible\Control\IVisibilityToggle;
use Nette\Application\UI\Multiplier;

trait TVisibilityTogglePresenter
{

	/** @var string|null */
	protected $visibilityTemplateFile;

	/**
	 * @param mixed $id
	 */
	abstract protected function getTarget($id): IVisible;

	protected function createComponentVisibilityToggle(IVisibilityToggle $factory): Multiplier
	{
		return new Multiplier(function ($id) use ($factory) {
			$component = $factory->create($this->getTarget($id));
			if ($this->visibilityTemplateFile !== null) {
				$component->setTemplateFile($this->visibilityTemplateFile);
			}
			return $component;
		});
	}

}
