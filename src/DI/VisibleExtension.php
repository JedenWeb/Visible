<?php declare(strict_types = 1);

namespace JedenWeb\Visible\DI;

use JedenWeb\Visible\Control\IVisibilityToggle;
use Nette\DI\CompilerExtension;

class VisibleExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$container = $this->getContainerBuilder();

		$container->addFactoryDefinition($this->prefix('toggle'))
			->setImplement(IVisibilityToggle::class);
	}

}
