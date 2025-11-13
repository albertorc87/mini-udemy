<?php

declare(strict_types=1);

namespace Udemy\User\User\Infrastructure\Service;

use Firebase\JWT\JWT;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Udemy\User\User\Application\Service\ConfirmationUrlGenerator;

final class JWTConfirmationUserUrlGenerator implements ConfirmationUrlGenerator
{
	public function __construct(
		private readonly UrlGeneratorInterface $urlGenerator,
		private readonly string $secret,
	) {
	}

	public function generate(string $userId): string
	{
		$now = time();
		$expiration = $now + (24 * 60 * 60);
		
		$payload = [
			'userId' => $userId,
			'type' => 'email_confirmation',
			'iat' => $now,
			'exp' => $expiration,
		];
		
		// Generar el token JWT
		$jwt = JWT::encode($payload, $this->secret, 'HS256');
		
		// Generar la URL con el token como parámetro
		$confirmationUrl = $this->urlGenerator->generate(
			'confirm_user',
			['jwtToken' => $jwt],
			UrlGeneratorInterface::ABSOLUTE_URL
		);
		
		return $confirmationUrl;
	}
}

