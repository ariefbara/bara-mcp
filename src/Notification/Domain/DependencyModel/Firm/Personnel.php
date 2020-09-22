<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\Model\ {
    Firm,
    Firm\Personnel\PersonnelMailNotification
};
use Resources\ {
    Application\Service\RecipientInterface,
    Domain\Model\Mail\Recipient,
    Domain\ValueObject\PersonName
};

class Personnel
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

    /**
     *
     * @var bool
     */
    protected $removed;
    
    protected function __construct()
    {
        ;
    }
    
    public function getMailRecipient(): RecipientInterface
    {
        return new Recipient($this->email, $this->name);
    }
    
    public function getName(): string
    {
        return $this->name->getFullName();
    }
    
    public function createMailNotification(): PersonnelMailNotification
    {
        return new PersonnelMailNotification($this);
    }
    
}
