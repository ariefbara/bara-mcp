<?php

namespace Notification\Application\Service;

class SendImmediateMail
{

    /**
     *
     * @var RecipientRepository
     */
    protected $recipientRepository;

    /**
     *
     * @var MailSender
     */
    protected $mailSender;

    public function __construct(RecipientRepository $recipientRepository, MailSender $mailSender)
    {
        $this->recipientRepository = $recipientRepository;
        $this->mailSender = $mailSender;
    }

    public function execute()
    {
        $sendmailPath = dirname(__DIR__, 4) . "/scripts/sendmail.php";
        exec("php $sendmailPath > /dev/null 2>/dev/null &");
        
//        foreach ($this->recipientRepository->allRecipientsWithZeroAttempt() as $recipient) {
//            $this->mailSender->send($recipient);
//        }
//        $this->recipientRepository->update();
    }

}
