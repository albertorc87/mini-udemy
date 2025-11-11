<?php

declare(strict_types=1);

namespace Udemy\Course\Lesson\Domain;

use Udemy\Course\Course\Domain\Course;
use Udemy\Course\Lesson\Domain\LessonContent;
use Udemy\Course\Lesson\Domain\LessonDescription;
use Udemy\Course\Lesson\Domain\LessonId;
use Udemy\Course\Lesson\Domain\LessonOrder;
use Udemy\Course\Lesson\Domain\LessonTitle;
use Udemy\Course\Lesson\Domain\LessonVideoUrl;

/**
 * Entidad Lesson
 * El mapeo XML está en config/mappings/Course/Lesson/Lesson.orm.xml
 */
class Lesson
{
    private LessonId $id;
    private Course $course;
    private LessonTitle $title;
    private ?LessonDescription $description;
    private ?LessonVideoUrl $videoUrl;
    private ?LessonContent $content;
    private LessonOrder $order;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        LessonId $id,
        Course $course,
        LessonTitle $title,
        LessonOrder $order,
        ?LessonDescription $description = null,
        ?LessonVideoUrl $videoUrl = null,
        ?LessonContent $content = null
    ) {
        $this->id = $id;
        $this->course = $course;
        $this->title = $title;
        $this->description = $description;
        $this->videoUrl = $videoUrl;
        $this->content = $content;
        $this->order = $order;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): LessonId
    {
        return $this->id;
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

    public function getTitle(): LessonTitle
    {
        return $this->title;
    }

    public function setTitle(LessonTitle $title): void
    {
        $this->title = $title;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getDescription(): ?LessonDescription
    {
        return $this->description;
    }

    public function setDescription(?LessonDescription $description): void
    {
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getVideoUrl(): ?LessonVideoUrl
    {
        return $this->videoUrl;
    }

    public function setVideoUrl(?LessonVideoUrl $videoUrl): void
    {
        $this->videoUrl = $videoUrl;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getContent(): ?LessonContent
    {
        return $this->content;
    }

    public function setContent(?LessonContent $content): void
    {
        $this->content = $content;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getOrder(): LessonOrder
    {
        return $this->order;
    }

    public function setOrder(LessonOrder $order): void
    {
        $this->order = $order;
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

