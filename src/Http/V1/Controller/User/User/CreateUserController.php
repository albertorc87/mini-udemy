<?php

declare(strict_types=1);

namespace Udemy\Http\V1\Controller\User\User;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Udemy\Http\V1\Request\User\User\CreateUserRequest;
use Udemy\Shared\Domain\Bus\Command\CommandBus;
use Udemy\User\User\Application\Command\CreateUserCommand;

final class CreateUserController
{
	public function __construct(
		private readonly CommandBus $commandBus,
		private readonly ValidatorInterface $validator
	) {
	}

	public function __invoke(Request $request): JsonResponse
	{
		$data = json_decode($request->getContent(), true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			return new JsonResponse(
				['error' => 'Invalid JSON'],
				Response::HTTP_BAD_REQUEST
			);
		}

		// Crear Request DTO
		$createUserRequest = CreateUserRequest::fromArray($data);

		// Validar tempranamente
		$violations = $this->validator->validate($createUserRequest);

		if (count($violations) > 0) {
			$errors = [];
			foreach ($violations as $violation) {
				$propertyPath = $violation->getPropertyPath();
				$errors[$propertyPath] = $violation->getMessage();
			}

			return new JsonResponse(
				['errors' => $errors],
				Response::HTTP_BAD_REQUEST
			);
		}

		// Crear el comando desde el Request validado
		$command = new CreateUserCommand(
			email: $createUserRequest->email,
			password: $createUserRequest->password,
			name: $createUserRequest->name,
			avatarUrl: $createUserRequest->avatarUrl
		);

		try {
			// Despachar el comando (no retorna nada según CQRS)
			$this->commandBus->dispatch($command);

			return new JsonResponse(
				[
					'message' => 'User created successfully',
					'email' => $createUserRequest->email
				],
				Response::HTTP_CREATED
			);
		} catch (\DomainException $e) {
			return new JsonResponse(
				['error' => $e->getMessage()],
				Response::HTTP_CONFLICT
			);
		} catch (\InvalidArgumentException $e) {
			return new JsonResponse(
				['error' => $e->getMessage()],
				Response::HTTP_BAD_REQUEST
			);
		} 
	}
}

