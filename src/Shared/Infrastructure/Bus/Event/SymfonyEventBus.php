<?php

declare(strict_types=1);

namespace Udemy\Shared\Infrastructure\Bus\Event;

use Symfony\Component\Messenger\MessageBusInterface;
use Udemy\Shared\Domain\Bus\Event\DomainEvent;
use Udemy\Shared\Domain\Bus\Event\EventBus;

final class SymfonyEventBus implements EventBus
{
	public function __construct(
		private readonly MessageBusInterface $messageBus
	) {
	}

	public function publish(DomainEvent ...$events): void
	{
		foreach ($events as $event) {
			$this->messageBus->dispatch($event);
		}
	}
}

