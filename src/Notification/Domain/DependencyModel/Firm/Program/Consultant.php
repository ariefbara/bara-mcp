<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\ {
    Personnel,
    Program,
    Program\Consultant\ConsultantMailNotification
};
use Resources\Domain\Model\Mail\Recipient;

class Consultant
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Personnel
     */
    protected $personnel;

    /**
     *
     * @var bool
     */
    protected $removed;

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        ;
    }

    public function getPersonnelMailRecipient(): Recipient
    {
        return $this->personnel->getMailRecipient();
    }
    
    public function getPersonnelName(): string
    {
        return $this->personnel->getName();
    }
    
    public function createMailNotification(): ConsultantMailNotification
    {
        $personnelMailNotification = $this->personnel->createMailNotification();
        return new ConsultantMailNotification($this, $personnelMailNotification);
    }

}
