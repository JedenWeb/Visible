<?php declare(strict_types = 1);

namespace JedenWeb\Visible;

interface IVisible
{

	/**
	 * @return mixed
	 */
	public function getId();

	public function isVisible(): bool;

	public function setVisible(bool $visible): void;

	public function toggleVisibility(): void;

}
