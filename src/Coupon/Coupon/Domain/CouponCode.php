<?php

declare(strict_types=1);

namespace Udemy\Coupon\Coupon\Domain;

use InvalidArgumentException;
use Udemy\Shared\Domain\ValueObject\StringValueObject;

final class CouponCode extends StringValueObject
{
	private const MAX_LENGTH = 50;

	public function __construct(string $value)
	{
		$this->validateLength($value);
		parent::__construct($value);
	}

	private function validateLength(string $value): void
	{
		if (strlen($value) > self::MAX_LENGTH) {
			throw new InvalidArgumentException(
				sprintf('Coupon code cannot exceed %d characters, got %d', self::MAX_LENGTH, strlen($value))
			);
		}
	}
}

