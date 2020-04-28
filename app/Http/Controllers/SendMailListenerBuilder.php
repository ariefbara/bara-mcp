<?php

namespace App\Http\Controllers;

use Resources\{
    Application\Listener\SendMailListener,
    Application\Service\SendMail,
    Infrastructure\MailManager\SwiftMailer
};
use Swift_Mailer;
use Swift_SmtpTransport;
use function env;

class SendMailListenerBuilder
{

    public static function build(): SendMailListener
    {
        $transport = new Swift_SmtpTransport(
                env('MAIL_SERVER_HOST'), env('MAIL_SERVER_PORT'), env('MAIL_SERVER_ENCRYPTION'));
        $transport->setUsername(env('MAIL_SERVER_USERNAME'));
        $transport->setPassword(env('MAIL_SERVER_PASSWORD'));
        $vendor = new Swift_Mailer($transport);
        $mailer = new SwiftMailer($vendor);
        $senderName = env('MAIL_SERVER_NAME');
        $senderAddress = env('MAIL_SERVER_ADDRESS');

        $mailSend = new SendMail($mailer, $senderName, $senderAddress);
        return new SendMailListener($mailSend);
    }

}
