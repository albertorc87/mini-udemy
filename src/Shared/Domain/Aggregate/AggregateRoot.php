<?php

declare(strict_types=1);

namespace Udemy\Shared\Domain\Aggregate;

use Udemy\Shared\Domain\Bus\Event\DomainEvent;

abstract class AggregateRoot
{
	private array $domainEvents = [];

	final public function pullDomainEvents(): array
	{
		$domainEvents = $this->domainEvents;
		$this->domainEvents = [];

		return $domainEvents;
	}

	final protected function record(DomainEvent $domainEvent): void
	{
		$this->domainEvents[] = $domainEvent;
	}
}