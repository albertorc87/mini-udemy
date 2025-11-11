<?php

declare(strict_types=1);

namespace Udemy\Shared\Infrastructure\Persistence\Doctrine\Type;

use Udemy\Order\Order\Domain\OrderId;

/**
 * Custom Type para mapear OrderId Value Object
 */
final class OrderIdType extends UlidValueObjectType
{
	public function getName(): string
	{
		return 'order_id';
	}

	protected function getValueObjectClass(): string
	{
		return OrderId::class;
	}
}

