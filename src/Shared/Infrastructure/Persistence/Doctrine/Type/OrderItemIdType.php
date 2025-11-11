<?php

declare(strict_types=1);

namespace Udemy\Shared\Infrastructure\Persistence\Doctrine\Type;

use Udemy\Order\Item\Domain\OrderItemId;

/**
 * Custom Type para mapear OrderItemId Value Object
 */
final class OrderItemIdType extends UlidValueObjectType
{
	public function getName(): string
	{
		return 'order_item_id';
	}

	protected function getValueObjectClass(): string
	{
		return OrderItemId::class;
	}
}

