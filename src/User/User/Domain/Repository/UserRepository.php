<?php

declare(strict_types=1);

namespace Udemy\User\User\Domain\Repository;

use Udemy\User\User\Domain\User;
use Udemy\User\User\Domain\UserEmail;
use Udemy\User\User\Domain\UserId;

interface UserRepository
{
	public function save(User $user): void;

	public function findByEmail(UserEmail $email): ?User;

	public function findById(UserId $id): ?User;
}

