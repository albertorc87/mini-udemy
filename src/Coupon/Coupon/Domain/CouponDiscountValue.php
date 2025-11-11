<?php

declare(strict_types=1);

namespace Udemy\Coupon\Coupon\Domain;

use InvalidArgumentException;
use Udemy\Shared\Domain\ValueObject\DecimalValueObject;

final class CouponDiscountValue extends DecimalValueObject
{
	public function __construct(float $value)
	{
		$this->validate($value);
		parent::__construct($value);
	}

	private function validate(float $value): void
	{
		if ($value <= 0) {
			throw new InvalidArgumentException(
				sprintf('Coupon discount value must be greater than 0, got: %f', $value)
			);
		}
	}
}

