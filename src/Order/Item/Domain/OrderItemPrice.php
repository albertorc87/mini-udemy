<?php

declare(strict_types=1);

namespace Udemy\Order\Item\Domain;

use InvalidArgumentException;
use Udemy\Shared\Domain\ValueObject\DecimalValueObject;

final class OrderItemPrice extends DecimalValueObject
{
	public function __construct(float $value)
	{
		$this->validate($value);
		parent::__construct($value);
	}

	private function validate(float $value): void
	{
		if ($value < 0) {
			throw new InvalidArgumentException(
				sprintf('Order item price cannot be negative, got: %f', $value)
			);
		}
	}
}

