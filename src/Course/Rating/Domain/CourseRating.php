<?php

declare(strict_types=1);

namespace Udemy\Course\Rating\Domain;

use Udemy\Course\Course\Domain\Course;
use Udemy\Course\Rating\Domain\CourseRatingComment;
use Udemy\Course\Rating\Domain\CourseRatingId;
use Udemy\Course\Rating\Domain\CourseRatingRating;
use Udemy\User\User\Domain\User;

/**
 * Entidad CourseRating
 * Representa una valoración de un curso por un usuario
 * El mapeo XML está en config/doctrine/Course/Rating/CourseRating.orm.xml
 */
class CourseRating
{
    private CourseRatingId $id;
    private User $user;
    private Course $course;
    private CourseRatingRating $rating;
    private ?CourseRatingComment $comment;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        CourseRatingId $id,
        User $user,
        Course $course,
        CourseRatingRating $rating,
        ?CourseRatingComment $comment = null
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->course = $course;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): CourseRatingId
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

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): void
    {
        $this->course = $course;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getRating(): CourseRatingRating
    {
        return $this->rating;
    }

    public function setRating(CourseRatingRating $rating): void
    {
        $this->rating = $rating;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getComment(): ?CourseRatingComment
    {
        return $this->comment;
    }

    public function setComment(?CourseRatingComment $comment): void
    {
        $this->comment = $comment;
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
