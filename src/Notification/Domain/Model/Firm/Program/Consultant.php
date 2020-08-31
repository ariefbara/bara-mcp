<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\ {
    Personnel,
    Program
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

    protected function __construct()
    {
        ;
    }
    
    public function getPersonnelMailRecipient(): Recipient
    {
        return $this->personnel->getMailRecipient();
    }

}
