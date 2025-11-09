<?php
namespace Udemy\Shared\Domain;

use DateTimeImmutable;

interface Clock
{
public function now(): DateTimeImmutable;
}