<?php

declare(strict_types=1);

namespace Udemy\Coupon\Coupon\Domain;

use InvalidArgumentException;
use Udemy\Shared\Domain\ValueObject\IntValueObject;

final class CouponMaxUses extends IntValueObject
{
	public function __construct(int $value)
	{
		$this->validate($value);
		parent::__construct($value);
	}

	private function validate(int $value): void
	{
		if ($value <= 0) {
			throw new InvalidArgumentException(
				sprintf('Coupon max uses must be greater than 0, got: %d', $value)
			);
		}
	}
}

