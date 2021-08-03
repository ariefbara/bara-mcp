<?php

namespace Notification\Application\Service;

class SendMail
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
        foreach ($this->recipientRepository->allRecipientsWithZeroAttempt() as $recipient) {
            $this->mailSender->send($recipient);
        }
        $this->recipientRepository->update();
    }
}
