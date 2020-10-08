<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\ {
    Model\Firm\Personnel,
    SharedModel\canSendPersonalizeMail,
    SharedModel\MailMessage
};

class Consultant
{

    /**
     *
     * @var string
     */
    protected $programId;

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

    public function getPersonnelFullName(): string
    {
        
    }

    public function registerMailRecipient(canSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        
    }

}
