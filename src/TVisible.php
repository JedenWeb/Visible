<?php declare(strict_types = 1);

namespace JedenWeb\Visible;

use Doctrine\ORM\Mapping as ORM;

trait TVisible
{

	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 */
	protected $visible = true;

	public function isVisible(): bool
	{
		return $this->visible;
	}

	public function setVisible(bool $visible): void
	{
		$this->visible = $visible;
	}

	public function toggleVisibility(): void
	{
		$this->visible = !$this->visible;
	}

}
