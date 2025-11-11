<?php

declare(strict_types=1);

namespace Udemy\User\User\Infrastructure\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Udemy\User\User\Application\Service\EmailSender;

final class MailerEmailSender implements EmailSender
{
	public function __construct(
		private readonly MailerInterface $mailer,
		private readonly string $fromEmail
	) {
	}

	public function sendConfirmationEmail(string $to, string $name): void
	{
		$email = (new Email())
			->from($this->fromEmail)
			->to($to)
			->subject('Bienvenido a Mini Udemy - Confirma tu cuenta')
			->html($this->getEmailTemplate($name));

		$this->mailer->send($email);
	}

	private function getEmailTemplate(string $name): string
	{
		return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de cuenta</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0;">
        <h1 style="margin: 0;">¡Bienvenido a Mini Udemy!</h1>
    </div>
    <div style="background-color: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px;">
        <p>Hola <strong>{$name}</strong>,</p>
        <p>Gracias por registrarte en Mini Udemy. Tu cuenta ha sido creada exitosamente.</p>
        <p>Ya puedes comenzar a explorar nuestros cursos y aprender algo nuevo cada día.</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="#" style="background-color: #4CAF50; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Acceder a mi cuenta</a>
        </div>
        <p style="color: #666; font-size: 14px; margin-top: 30px;">
            Si no creaste esta cuenta, puedes ignorar este email.
        </p>
    </div>
    <div style="text-align: center; margin-top: 20px; color: #999; font-size: 12px;">
        <p>&copy; 2024 Mini Udemy. Todos los derechos reservados.</p>
    </div>
</body>
</html>
HTML;
	}
}

