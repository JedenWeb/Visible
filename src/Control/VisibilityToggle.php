<?php declare(strict_types = 1);

namespace JedenWeb\Visible\Control;

use Doctrine\Common\Persistence\ObjectManager;
use JedenWeb\Visible\IVisible;
use Nette\Application\UI\Control;
use Nextras\Application\UI\SecuredLinksControlTrait;

class VisibilityToggle extends Control
{

	use SecuredLinksControlTrait {
		link as securedLink;
	}

	/** @var IVisible */
	private $target;

	/** @var ObjectManager */
	private $entityManager;

	/** @var string */
	private $templateFile = __DIR__ . '/VisibilityToggle.latte';

	public function __construct(IVisible $target, ObjectManager $entityManager)
	{
		$this->target = $target;
		$this->entityManager = $entityManager;
	}

	// @codingStandardsIgnoreLine
	public function link(string $destination, $args = []): string
	{
		if (method_exists($this->getPresenter(), 'createSecuredLink')) {
			return $this->securedLink($destination, $args);
		}

		return parent::link($destination, $args);
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
		if (strpos($templateFile, DIRECTORY_SEPARATOR) === false) {
			$templateFile = __DIR__ . DIRECTORY_SEPARATOR . $templateFile;
		}

		$this->templateFile = $templateFile;
	}

}
