<?php

declare(strict_types=1);

namespace Udemy\Shared\Infrastructure\Persistence\Doctrine\Type;

use Udemy\Coupon\Coupon\Domain\CouponId;

/**
 * Custom Type para mapear CouponId Value Object
 */
final class CouponIdType extends UlidValueObjectType
{
	public function getName(): string
	{
		return 'coupon_id';
	}

	protected function getValueObjectClass(): string
	{
		return CouponId::class;
	}
}

