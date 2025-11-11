<?php

declare(strict_types=1);

namespace Udemy\Course\Course\Domain;

use Udemy\Course\Course\Domain\CourseAverageRating;
use Udemy\Course\Course\Domain\CourseDescription;
use Udemy\Course\Course\Domain\CourseId;
use Udemy\Course\Course\Domain\CoursePrice;
use Udemy\Course\Course\Domain\CourseStatus;
use Udemy\Course\Course\Domain\CourseSubtitle;
use Udemy\Course\Course\Domain\CourseTitle;
use Udemy\Course\Course\Domain\CourseTotalRatings;
use Udemy\User\User\Domain\User;

/**
 * Entidad Course
 * El mapeo XML está en config/mappings/Course/Course/Course.orm.xml
 */
class Course
{
    private CourseId $id;
    private User $teacher;
    private CourseTitle $title;
    private ?CourseSubtitle $subtitle;
    private ?CourseDescription $description;
    private CoursePrice $price;
    private CourseAverageRating $averageRating;
    private CourseTotalRatings $totalRatings;
    private CourseStatus $status;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        CourseId $id,
        User $teacher,
        CourseTitle $title,
        CoursePrice $price,
        ?CourseSubtitle $subtitle = null,
        ?CourseDescription $description = null,
        ?CourseStatus $status = null,
        ?CourseAverageRating $averageRating = null,
        ?CourseTotalRatings $totalRatings = null
    ) {
        $this->id = $id;
        $this->teacher = $teacher;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->description = $description;
        $this->price = $price;
        $this->status = $status ?? new CourseStatus('draft');
        $this->averageRating = $averageRating ?? new CourseAverageRating(0.0);
        $this->totalRatings = $totalRatings ?? new CourseTotalRatings(0);
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): CourseId
    {
        return $this->id;
    }

    public function getTeacher(): User
    {
        return $this->teacher;
    }

    public function setTeacher(User $teacher): void
    {
        $this->teacher = $teacher;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getTitle(): CourseTitle
    {
        return $this->title;
    }

    public function setTitle(CourseTitle $title): void
    {
        $this->title = $title;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getSubtitle(): ?CourseSubtitle
    {
        return $this->subtitle;
    }

    public function setSubtitle(?CourseSubtitle $subtitle): void
    {
        $this->subtitle = $subtitle;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getDescription(): ?CourseDescription
    {
        return $this->description;
    }

    public function setDescription(?CourseDescription $description): void
    {
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getPrice(): CoursePrice
    {
        return $this->price;
    }

    public function setPrice(CoursePrice $price): void
    {
        $this->price = $price;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getAverageRating(): CourseAverageRating
    {
        return $this->averageRating;
    }

    public function setAverageRating(CourseAverageRating $averageRating): void
    {
        $this->averageRating = $averageRating;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getTotalRatings(): CourseTotalRatings
    {
        return $this->totalRatings;
    }

    public function setTotalRatings(CourseTotalRatings $totalRatings): void
    {
        $this->totalRatings = $totalRatings;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getStatus(): CourseStatus
    {
        return $this->status;
    }

    public function setStatus(CourseStatus $status): void
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

