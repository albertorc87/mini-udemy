<?php

declare(strict_types=1);

namespace Udemy\User\User\Infrastructure\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Udemy\User\User\Application\Service\EmailSender;

final class MailerEmailSender implements EmailSender
{
	public function __construct(
		private readonly MailerInterface $mailer,
		private readonly Environment $twig,
		private readonly string $fromEmail
	) {
	}

	public function sendConfirmationEmail(string $to, string $name, string $confirmationUrl): void
	{
		$email = (new Email())
			->from($this->fromEmail)
			->to($to)
			->subject('Bienvenido a Mini Udemy - Confirma tu cuenta')
			->html($this->twig->render('emails/user/registration/confirmation_email.html.twig', [
				'name' => $name,
				'confirmation_url' => $confirmationUrl,
			]));

		$this->mailer->send($email);
	}
}

