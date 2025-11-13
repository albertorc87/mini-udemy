<?php

declare(strict_types=1);

namespace Udemy\User\User\Application\Service;

interface EmailSender
{
	public function sendConfirmationEmail(string $to, string $name, string $confirmationUrl): void;
}

