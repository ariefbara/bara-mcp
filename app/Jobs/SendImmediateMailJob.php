<?php

namespace App\Jobs;

use Notification\Application\Service\RecipientRepository;
use Notification\Application\Service\SendImmediateMail;
use Notification\Infrastructure\MailManager\SwiftMailSender;
use Swift_Mailer;
use Swift_SmtpTransport;
use function env;

class SendImmediateMailJob extends Job
{

    /**
     * 
     * @var RecipientRepository
     */
    protected $recipientRepository;

    public function __construct(RecipientRepository $recipientRepository)
    {
        $this->recipientRepository = $recipientRepository;
    }

    public function handle()
    {
        $transport = new Swift_SmtpTransport(
                env('MAIL_SERVER_HOST'), env('MAIL_SERVER_PORT'), env('MAIL_SERVER_ENCRYPTION'));
        $transport->setUsername(env('MAIL_SERVER_USERNAME'));
        $transport->setPassword(env('MAIL_SERVER_PASSWORD'));
        $vendor = new Swift_Mailer($transport);
        $mailSender = new SwiftMailSender($vendor);
        (new SendImmediateMail($this->recipientRepository, $mailSender))->execute();
    }

}
