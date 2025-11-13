<?php

declare(strict_types=1);

namespace Udemy\Http\V1\Controller\User\User;

use DomainException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Udemy\Shared\Domain\Bus\Command\CommandBus;
use Udemy\User\User\Application\Command\ConfirmUserCommand;

final class ConfirmUserController
{
	public function __construct(
		private readonly CommandBus $commandBus,
	) {
	}

	public function __invoke(string $jwtToken): JsonResponse
	{

		$command = new ConfirmUserCommand(
			jwtToken: $jwtToken
		);

		try {
			$this->commandBus->dispatch($command);

			return new JsonResponse(
				['message' => 'User confirmed successfully'],
				Response::HTTP_OK
			);
		} catch (DomainException $e) {
			return new JsonResponse(
				['error' => $e->getMessage()],
				Response::HTTP_BAD_REQUEST
			);
		}
	}
}

