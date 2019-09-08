<?php declare(strict_types = 1);

namespace Tests;

use JedenWeb\Visible\IVisible;
use JedenWeb\Visible\TVisible;

class Entity implements IVisible
{

	use TVisible;

	public function getId(): int
	{
		return 1;
	}

}
