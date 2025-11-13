<?php

declare(strict_types=1);

namespace Udemy\User\User\Application\EventHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Udemy\User\User\Application\Service\ConfirmationUrlGenerator;
use Udemy\User\User\Application\Service\EmailSender;
use Udemy\User\User\Domain\Event\UserCreated;

#[AsMessageHandler]
final class SendUserConfirmationEmailHandler
{
	public function __construct(
		private readonly EmailSender $emailSender,
		private readonly ConfirmationUrlGenerator $confirmationUrlGenerator
	) {
	}

	public function __invoke(UserCreated $event): void
	{
		$userId = $event->aggregateId();
		$confirmationUrl = $this->confirmationUrlGenerator->generate(
			$userId
		);

		$this->emailSender->sendConfirmationEmail(
			$event->email(),
			$event->name(),
			$confirmationUrl
		);
	}
}

