<?php

namespace Udemy\Http\V1\Controller\Course\Course;

use Symfony\Component\HttpFoundation\JsonResponse;

final class CourseListController
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['message' => 'Hello, World!']);
    }
}