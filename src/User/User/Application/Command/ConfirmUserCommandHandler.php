<?php

declare(strict_types=1);

namespace Udemy\User\User\Application\Command;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Udemy\Shared\Domain\Bus\Command\Command;
use Udemy\Shared\Domain\Bus\Command\CommandHandler;
use Udemy\User\User\Application\Service\UserConfirmer;

#[AsMessageHandler]
final class ConfirmUserCommandHandler implements CommandHandler
{
	public function __construct(
		private readonly UserConfirmer $userConfirmer
	) {
	}

	public function handle(Command $command): void
	{
		if (!$command instanceof ConfirmUserCommand) {
			throw new \InvalidArgumentException('Command must be an instance of ConfirmUserCommand');
		}

		$this->userConfirmer->__invoke(
			$command->jwtToken
		);
	}

	/**
	 * Método __invoke para que Symfony Messenger lo detecte automáticamente
	 * sin necesidad de atributos
	 */
	public function __invoke(ConfirmUserCommand $command): void
	{
		$this->handle($command);
	}
}

