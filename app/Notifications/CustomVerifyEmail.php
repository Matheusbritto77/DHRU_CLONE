<?php

namespace App\Notifications;

use App\Support\RegistrationSettings;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmail
{
    public function toMail(object $notifiable): MailMessage
    {
        $settings = RegistrationSettings::get();
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject($settings['verification_email_subject'] ?? 'Verifique seu e-mail')
            ->greeting($settings['verification_email_greeting'] ?? 'Confirme seu cadastro')
            ->line($settings['verification_email_intro'] ?? 'Obrigado por criar sua conta. Antes de continuar, confirme seu e-mail clicando no botao abaixo.')
            ->action($settings['verification_email_button'] ?? 'Verificar e-mail', $verificationUrl)
            ->line($settings['verification_email_outro'] ?? 'Se voce nao criou esta conta, ignore esta mensagem.');
    }
}
