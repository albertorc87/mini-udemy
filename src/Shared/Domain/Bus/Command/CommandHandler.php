<?php

declare(strict_types=1);

namespace Udemy\Shared\Domain\Bus\Command;

interface CommandHandler
{
	public function handle(Command $command): void;
}

