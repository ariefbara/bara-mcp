<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\ {
    Model\Firm,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\MailMessage
};
use Resources\Domain\ValueObject\PersonName;

class Client
{
    /**
     *
     * @var Firm
     */
    protected $firm;

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
