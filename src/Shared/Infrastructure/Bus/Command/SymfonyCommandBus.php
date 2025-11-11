<?php

declare(strict_types=1);

namespace Udemy\Shared\Infrastructure\Bus\Command;

use Symfony\Component\Messenger\MessageBusInterface;
use Udemy\Shared\Domain\Bus\Command\Command;
use Udemy\Shared\Domain\Bus\Command\CommandBus;

final class SymfonyCommandBus implements CommandBus
{
	public function __construct(
		private readonly MessageBusInterface $messageBus
	) {
	}

	public function dispatch(Command $command): void
	{
		$this->messageBus->dispatch($command);
	}
}

