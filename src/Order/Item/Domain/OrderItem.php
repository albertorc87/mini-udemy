<?php

declare(strict_types=1);

namespace Udemy\Order\Item\Domain;

use Udemy\Course\Course\Domain\Course;
use Udemy\Order\Item\Domain\OrderItemId;
use Udemy\Order\Item\Domain\OrderItemPrice;
use Udemy\Order\Order\Domain\Order;

/**
 * Entidad OrderItem
 * Representa un item (curso) dentro de un pedido
 * El mapeo XML está en config/mappings/Order/Item/OrderItem.orm.xml
 */
class OrderItem
{
    private OrderItemId $id;
    private Order $order;
    private Course $course;
    private OrderItemPrice $price;
    private \DateTimeImmutable $createdAt;

    public function __construct(
        OrderItemId $id,
        Order $order,
        Course $course,
        OrderItemPrice $price
    ) {
        $this->id = $id;
        $this->order = $order;
        $this->course = $course;
        $this->price = $price;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): OrderItemId
    {
        return $this->id;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): void
    {
        $this->course = $course;
    }

    public function getPrice(): OrderItemPrice
    {
        return $this->price;
    }

    public function setPrice(OrderItemPrice $price): void
    {
        $this->price = $price;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}

