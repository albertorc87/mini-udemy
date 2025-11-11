<?php

declare(strict_types=1);

namespace Udemy\User\User\Application\Command;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Udemy\Shared\Domain\Bus\Command\Command;
use Udemy\Shared\Domain\Bus\Command\CommandHandler;
use Udemy\User\User\Application\Service\UserCreator;

#[AsMessageHandler]
final class CreateUserCommandHandler implements CommandHandler
{
	public function __construct(
		private readonly UserCreator $userCreator
	) {
	}

	public function handle(Command $command): void
	{
		if (!$command instanceof CreateUserCommand) {
			throw new \InvalidArgumentException('Command must be an instance of CreateUserCommand');
		}

		$this->userCreator->create(
			$command->email,
			$command->password,
			$command->name,
			$command->avatarUrl
		);
	}

	/**
	 * Método __invoke para que Symfony Messenger lo detecte automáticamente
	 * sin necesidad de atributos
	 */
	public function __invoke(CreateUserCommand $command): void
	{
		$this->handle($command);
	}
}

