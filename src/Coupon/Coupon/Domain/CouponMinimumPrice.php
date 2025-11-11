<?php

declare(strict_types=1);

namespace Udemy\Coupon\Coupon\Domain;

use InvalidArgumentException;
use Udemy\Shared\Domain\ValueObject\DecimalValueObject;

final class CouponMinimumPrice extends DecimalValueObject
{
	private const MIN_VALUE = 10.99;

	public function __construct(float $value)
	{
		$this->validate($value);
		parent::__construct($value);
	}

	private function validate(float $value): void
	{
		if ($value < self::MIN_VALUE) {
			throw new InvalidArgumentException(
				sprintf('Coupon minimum price must be at least %.2f, got: %f', self::MIN_VALUE, $value)
			);
		}
	}
}

