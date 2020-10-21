<?php

namespace Notification\Infrastructure\MailManager;

use Notification\ {
    Application\Service\MailSender,
    Domain\SharedModel\Mail\Recipient
};
use Swift_Mailer;
use Swift_Message;

class SwiftMailSender implements MailSender
{
    /**
     *
     * @var Swift_Mailer
     */
    protected $vendor;
    
    function __construct(Swift_Mailer $vendor)
    {
        $this->vendor = $vendor;
    }

    public function send(Recipient $recipient): void
    {
        $message = (new Swift_Message())
            ->setSubject($recipient->getSubject())
            ->setFrom($recipient->getSenderMailAddress(), $recipient->getSenderName())
            ->setBody($recipient->getMessage())
            ->setTo($recipient->getRecipientMailAddress(), $recipient->getRecipientName());
        
        if (!empty($recipient->getHtmlMessage())) {
            $message->addPart($recipient->getHtmlMessage(), "text/html");
        }
        
        
        if (1 == $this->vendor->send($message)) {
            $recipient->sendSuccessful();
        }
        $recipient->increaseAttempt();
    }

}
