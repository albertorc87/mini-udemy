<?php

declare(strict_types=1);

namespace Udemy\Http\V1\Request\User\User;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserRequest
{
	#[Assert\NotBlank(message: 'Email is required')]
	#[Assert\Email(message: 'Email must be a valid email address')]
	#[Assert\Length(max: 255, maxMessage: 'Email cannot exceed 255 characters')]
	public readonly string $email;

	#[Assert\NotBlank(message: 'Password is required')]
	#[Assert\Length(
		min: 8,
		minMessage: 'Password must be at least 8 characters long'
	)]
	public readonly string $password;

	#[Assert\NotBlank(message: 'Name is required')]
	#[Assert\Length(max: 255, maxMessage: 'Name cannot exceed 255 characters')]
	public readonly string $name;

	#[Assert\Url(message: 'Avatar URL must be a valid URL')]
	#[Assert\Length(max: 500, maxMessage: 'Avatar URL cannot exceed 500 characters')]
	public readonly ?string $avatarUrl;

	public function __construct(
		string $email,
		string $password,
		string $name,
		?string $avatarUrl = null
	) {
		$this->email = $email;
		$this->password = $password;
		$this->name = $name;
		$this->avatarUrl = $avatarUrl;
	}

	public static function fromArray(array $data): self
	{
		return new self(
			email: $data['email'] ?? '',
			password: $data['password'] ?? '',
			name: $data['name'] ?? '',
			avatarUrl: $data['avatarUrl'] ?? null
		);
	}
}

