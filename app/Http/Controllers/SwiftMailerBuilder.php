<?php

namespace App\Http\Controllers;

use Resources\Infrastructure\MailManager\SwiftMailer;
use Swift_Mailer;
use Swift_SmtpTransport;
use function env;

class SwiftMailerBuilder
{
    public static function build(): SwiftMailer
    {
        $transport = new Swift_SmtpTransport(
                env('MAIL_SERVER_HOST'), env('MAIL_SERVER_PORT'), env('MAIL_SERVER_ENCRYPTION'));
        $transport->setUsername(env('MAIL_SERVER_USERNAME'));
        $transport->setPassword(env('MAIL_SERVER_PASSWORD'));
        $vendor = new Swift_Mailer($transport);
        return new SwiftMailer($vendor);
    }
}
