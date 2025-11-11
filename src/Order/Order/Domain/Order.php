<?php

declare(strict_types=1);

namespace Udemy\Order\Order\Domain;

use Udemy\Coupon\Coupon\Domain\Coupon;
use Udemy\Order\Order\Domain\OrderDiscount;
use Udemy\Order\Order\Domain\OrderId;
use Udemy\Order\Order\Domain\OrderStatus;
use Udemy\Order\Order\Domain\OrderSubtotal;
use Udemy\Order\Order\Domain\OrderTotal;
use Udemy\User\User\Domain\User;

/**
 * Entidad Order
 * Representa un pedido realizado por un usuario
 * El mapeo XML está en config/mappings/Order/Order/Order.orm.xml
 */
class Order
{
    private OrderId $id;
    private User $user;
    private ?Coupon $coupon;
    private OrderSubtotal $subtotal;
    private OrderDiscount $discount;
    private OrderTotal $total;
    private OrderStatus $status;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        OrderId $id,
        User $user,
        OrderSubtotal $subtotal,
        OrderTotal $total,
        ?Coupon $coupon = null,
        ?OrderDiscount $discount = null,
        ?OrderStatus $status = null
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->coupon = $coupon;
        $this->subtotal = $subtotal;
        $this->discount = $discount ?? new OrderDiscount(0.0);
        $this->total = $total;
        $this->status = $status ?? new OrderStatus('pending');
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): OrderId
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCoupon(): ?Coupon
    {
        return $this->coupon;
    }

    public function setCoupon(?Coupon $coupon): void
    {
        $this->coupon = $coupon;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getSubtotal(): OrderSubtotal
    {
        return $this->subtotal;
    }

    public function setSubtotal(OrderSubtotal $subtotal): void
    {
        $this->subtotal = $subtotal;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getDiscount(): OrderDiscount
    {
        return $this->discount;
    }

    public function setDiscount(OrderDiscount $discount): void
    {
        $this->discount = $discount;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getTotal(): OrderTotal
    {
        return $this->total;
    }

    public function setTotal(OrderTotal $total): void
    {
        $this->total = $total;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): void
    {
        $this->status = $status;
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

