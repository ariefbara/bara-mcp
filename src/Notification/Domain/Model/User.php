<?php

namespace Notification\Domain\Model;

use Notification\Domain\SharedModel\ {
    CanSendPersonalizeMail,
    MailMessage
};
use Resources\Domain\ValueObject\PersonName;

class User
{
    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var PersonName
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $email;
    
    protected function __construct()
    {
        ;
    }
    
    public function getFullName(): string
    {
        return $this->name->getFullName();
    }
    
    public function registerAsMailRecipient(CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $mailMessage = $mailMessage->appendRecipientFirstNameInGreetings($this->name->getFirstName());
        
        $mailGenerator->addMail($mailMessage, $this->email, $this->name->getFullName());
    }
}
