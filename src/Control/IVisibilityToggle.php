<?php declare(strict_types = 1);

namespace JedenWeb\Visible\Control;

use JedenWeb\Visible\IVisible;

interface IVisibilityToggle
{

	public function create(IVisible $target): VisibilityToggle;

}
