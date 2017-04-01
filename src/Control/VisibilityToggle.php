<?php declare(strict_types = 1);

namespace JedenWeb\Visible\Control;

use JedenWeb\Visible\IVisible;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nextras\Application\UI\SecuredLinksControlTrait;

class VisibilityToggle extends Control
{

	use SecuredLinksControlTrait {
		link as securedLink;
	}

	/** @var IVisible */
	private $target;

	/** @var EntityManager */
	private $entityManager;

	/** @var string */
	private $templateFile = __DIR__ . '/VisibilityToggle.latte';

	public function __construct(IVisible $target, EntityManager $entityManager)
	{
		parent::__construct();

		$this->target = $target;
		$this->entityManager = $entityManager;
	}

	// @codingStandardsIgnoreLine
	public function link($destination, $args = [])
	{
		if (method_exists($this->getPresenter(), 'createSecuredLink')) {
			return $this->securedLink($destination, $args);
		} else {
			return parent::link($destination, $args);
		}
	}

	/**
	 * @secured
	 */
	public function handleToggleVisibility(): void
	{
		$this->target->toggleVisibility();

		$this->entityManager->flush();

		if ($this->getPresenter()->isAjax()) {
			$this->redrawControl();
		} else {
			$this->redirect('this');
		}
	}

	public function render(): void
	{
		$this->template->target = $this->target;
		$this->template->setFile($this->templateFile);
		$this->template->render();
	}

	public function setTemplateFile(string $templateFile): void
	{
		$this->templateFile = $templateFile;
	}

}
