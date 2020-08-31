<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\Model\Firm;
use Resources\Domain\ {
    Model\Mail\Recipient,
    ValueObject\PersonName
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
    
    public function getMailRecipient(): Recipient
    {
        return new Recipient($this->email, $this->name);
    }
}
