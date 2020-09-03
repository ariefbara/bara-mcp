<?php

namespace Resources\Infrastructure\MailManager;

use Resources\Application\Service\ {
    Mailer,
    MailInterface
};
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;

class SwiftMailer implements Mailer
{
    /**
     *
     * @var Swift_Mailer
     */
    protected $vendor;
    protected $failedRecipients = null;
    
    function __construct(Swift_Mailer $vendor)
    {
        $this->vendor = $vendor;
        $this->failedRecipients = null;
    }

    
    public function send(MailInterface $mail): void
    {
        $recipients = [];
        foreach ($mail->getRecipients() as $recipient) {
            $recipients[$recipient->getMailAddress()] = $recipient->getFullName();
        }
        
        $message = (new Swift_Message())
            ->setSubject($mail->getSubject())
            ->setFrom($mail->getSenderMailAddress(), $mail->getSenderName())
            ->setBody($mail->getBody())
            ->setTo($recipients);
        
        if (!empty($mail->getAlternativeBody())) {
            $message->addPart($mail->getAlternativeBody(), 'text/html');
        }
        
        foreach ($mail->getDynamicAttachments() as $dynamicAttachment) {
            $attachment = (new Swift_Attachment())
                ->setFilename($dynamicAttachment->getFileName())
                ->setContentType($dynamicAttachment->getContentType())
                ->setBody($dynamicAttachment->getContent());
            $message->attach($attachment);
        }
        
        $this->vendor->send($message, $this->failedRecipients);
    }

}
