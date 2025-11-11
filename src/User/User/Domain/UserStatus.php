<?php

declare(strict_types=1);

namespace Udemy\User\User\Domain;

use InvalidArgumentException;
use Udemy\Shared\Domain\ValueObject\IntValueObject;

final class UserStatus extends IntValueObject
{
    public function __construct(int $value)
    {
        $this->validate($value);
        parent::__construct($value);
    }

    private function validate(int $value): void
    {
        $validValues = [
            UserStatusEnum::BANNED->value,
            UserStatusEnum::PENDING->value,
            UserStatusEnum::ACTIVE->value,
        ];

        if (!in_array($value, $validValues, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid user status: %d. Valid values are: %s', $value, implode(', ', $validValues))
            );
        }
    }

    public static function banned(): self
    {
        return new self(UserStatusEnum::BANNED->value);
    }

    public static function pending(): self
    {
        return new self(UserStatusEnum::PENDING->value);
    }

    public static function active(): self
    {
        return new self(UserStatusEnum::ACTIVE->value);
    }

    public function isBanned(): bool
    {
        return $this->value() === UserStatusEnum::BANNED->value;
    }

    public function isPending(): bool
    {
        return $this->value() === UserStatusEnum::PENDING->value;
    }

    public function isActive(): bool
    {
        return $this->value() === UserStatusEnum::ACTIVE->value;
    }
}

