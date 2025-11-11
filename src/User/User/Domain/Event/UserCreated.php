<?php

declare(strict_types=1);

namespace Udemy\User\User\Domain\Event;

use Udemy\Shared\Domain\Bus\Event\DomainEvent;

final class UserCreated extends DomainEvent
{
	public function __construct(
		string $aggregateId,
		private readonly string $email,
		private readonly string $name,
		string $eventId = null,
		string $occurredOn = null
	) {
		parent::__construct($aggregateId, $eventId, $occurredOn);
	}

	public static function eventName(): string
	{
		return 'user.created';
	}

	public function email(): string
	{
		return $this->email;
	}

	public function name(): string
	{
		return $this->name;
	}

	public function toPrimitives(): array
	{
		return [
			'email' => $this->email,
			'name' => $this->name,
		];
	}

	public static function fromPrimitives(
		string $aggregateId,
		array $body,
		string $eventId,
		string $occurredOn
	): self {
		return new self(
			$aggregateId,
			$body['email'],
			$body['name'],
			$eventId,
			$occurredOn
		);
	}
}

