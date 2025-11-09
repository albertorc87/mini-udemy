<?php
namespace Udemy\Shared\Infrastructure\Service;

use Udemy\Shared\Domain\Clock;
use DateTimeImmutable;

class SystemClock implements Clock
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }
}