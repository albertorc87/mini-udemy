<?php

declare(strict_types=1);

namespace Udemy\Coupon\Coupon\Domain;

use Udemy\Coupon\Coupon\Domain\CouponCode;
use Udemy\Coupon\Coupon\Domain\CouponCurrentUses;
use Udemy\Coupon\Coupon\Domain\CouponDiscountType;
use Udemy\Coupon\Coupon\Domain\CouponDiscountValue;
use Udemy\Coupon\Coupon\Domain\CouponId;
use Udemy\Coupon\Coupon\Domain\CouponIsActive;
use Udemy\Coupon\Coupon\Domain\CouponIsGeneral;
use Udemy\Coupon\Coupon\Domain\CouponMaxUses;
use Udemy\Coupon\Coupon\Domain\CouponMinimumPrice;
use Udemy\User\User\Domain\User;

/**
 * Entidad Coupon
 * El mapeo XML está en config/mappings/Coupon/Coupon/Coupon.orm.xml
 */
class Coupon
{
    private CouponId $id;
    private CouponCode $code;
    private CouponDiscountType $discountType;
    private CouponDiscountValue $discountValue;
    private CouponMinimumPrice $minimumPrice;
    private ?User $teacher;
    private CouponIsGeneral $isGeneral;
    private ?\DateTimeImmutable $validFrom;
    private ?\DateTimeImmutable $validUntil;
    private ?CouponMaxUses $maxUses;
    private CouponCurrentUses $currentUses;
    private CouponIsActive $isActive;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        CouponId $id,
        CouponCode $code,
        CouponDiscountType $discountType,
        CouponDiscountValue $discountValue,
        CouponMinimumPrice $minimumPrice,
        ?User $teacher = null,
        ?CouponIsGeneral $isGeneral = null,
        ?\DateTimeImmutable $validFrom = null,
        ?\DateTimeImmutable $validUntil = null,
        ?CouponMaxUses $maxUses = null,
        ?CouponCurrentUses $currentUses = null,
        ?CouponIsActive $isActive = null
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->discountType = $discountType;
        $this->discountValue = $discountValue;
        $this->minimumPrice = $minimumPrice;
        $this->teacher = $teacher;
        $this->isGeneral = $isGeneral ?? new CouponIsGeneral(false);
        $this->validFrom = $validFrom;
        $this->validUntil = $validUntil;
        $this->maxUses = $maxUses;
        $this->currentUses = $currentUses ?? new CouponCurrentUses(0);
        $this->isActive = $isActive ?? new CouponIsActive(true);
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): CouponId
    {
        return $this->id;
    }

    public function getCode(): CouponCode
    {
        return $this->code;
    }

    public function setCode(CouponCode $code): void
    {
        $this->code = $code;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getDiscountType(): CouponDiscountType
    {
        return $this->discountType;
    }

    public function setDiscountType(CouponDiscountType $discountType): void
    {
        $this->discountType = $discountType;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getDiscountValue(): CouponDiscountValue
    {
        return $this->discountValue;
    }

    public function setDiscountValue(CouponDiscountValue $discountValue): void
    {
        $this->discountValue = $discountValue;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getMinimumPrice(): CouponMinimumPrice
    {
        return $this->minimumPrice;
    }

    public function setMinimumPrice(CouponMinimumPrice $minimumPrice): void
    {
        $this->minimumPrice = $minimumPrice;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): void
    {
        $this->teacher = $teacher;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getIsGeneral(): CouponIsGeneral
    {
        return $this->isGeneral;
    }

    public function setIsGeneral(CouponIsGeneral $isGeneral): void
    {
        $this->isGeneral = $isGeneral;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getValidFrom(): ?\DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function setValidFrom(?\DateTimeImmutable $validFrom): void
    {
        $this->validFrom = $validFrom;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getValidUntil(): ?\DateTimeImmutable
    {
        return $this->validUntil;
    }

    public function setValidUntil(?\DateTimeImmutable $validUntil): void
    {
        $this->validUntil = $validUntil;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getMaxUses(): ?CouponMaxUses
    {
        return $this->maxUses;
    }

    public function setMaxUses(?CouponMaxUses $maxUses): void
    {
        $this->maxUses = $maxUses;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCurrentUses(): CouponCurrentUses
    {
        return $this->currentUses;
    }

    public function setCurrentUses(CouponCurrentUses $currentUses): void
    {
        $this->currentUses = $currentUses;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getIsActive(): CouponIsActive
    {
        return $this->isActive;
    }

    public function setIsActive(CouponIsActive $isActive): void
    {
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}

