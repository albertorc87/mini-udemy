<?php

declare(strict_types=1);

namespace Udemy\User\User\Application\Service;

interface ConfirmationUrlGenerator
{
	public function generate(string $userId): string;
}

