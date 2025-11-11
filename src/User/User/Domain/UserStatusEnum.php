<?php

declare(strict_types=1);

namespace Udemy\User\User\Domain;

enum UserStatusEnum: int
{
    case BANNED = -1;
    case PENDING = 0;
    case ACTIVE = 1;
}

