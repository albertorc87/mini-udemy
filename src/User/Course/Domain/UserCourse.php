<?php

declare(strict_types=1);

namespace Udemy\User\Course\Domain;

use Udemy\Course\Course\Domain\Course;
use Udemy\User\Course\Domain\UserCourseId;
use Udemy\User\User\Domain\User;

/**
 * Entidad UserCourse
 * Representa la relación entre un usuario y un curso que ha comprado
 * El mapeo XML está en config/mappings/User/Course/UserCourse.orm.xml
 */
class UserCourse
{
    private UserCourseId $id;
    private User $user;
    private Course $course;
    private \DateTimeImmutable $purchasedAt;

    public function __construct(
        UserCourseId $id,
        User $user,
        Course $course,
        ?\DateTimeImmutable $purchasedAt = null
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->course = $course;
        $this->purchasedAt = $purchasedAt ?? new \DateTimeImmutable();
    }

    public function getId(): UserCourseId
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
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): void
    {
        $this->course = $course;
    }

    public function getPurchasedAt(): \DateTimeImmutable
    {
        return $this->purchasedAt;
    }

    public function setPurchasedAt(\DateTimeImmutable $purchasedAt): void
    {
        $this->purchasedAt = $purchasedAt;
    }
}

