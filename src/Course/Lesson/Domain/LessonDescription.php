<?php

declare(strict_types=1);

namespace Udemy\Course\Lesson\Domain;

use InvalidArgumentException;
use Udemy\Shared\Domain\ValueObject\StringValueObject;

final class LessonDescription extends StringValueObject
{
	// TEXT en PostgreSQL puede almacenar hasta 1GB, pero ponemos un límite razonable
	private const MAX_LENGTH = 65535; // Similar a TEXT en MySQL

	public function __construct(string $value)
	{
		$this->validateLength($value);
		parent::__construct($value);
	}

	private function validateLength(string $value): void
	{
		if (strlen($value) > self::MAX_LENGTH) {
			throw new InvalidArgumentException(
				sprintf('Lesson description cannot exceed %d characters, got %d', self::MAX_LENGTH, strlen($value))
			);
		}
	}
}

