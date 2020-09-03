<?php

namespace Notification\Domain\Model\User;

use Notification\Domain\Model\SharedEntity\KonsultaMailRecipientInterface;

class UserParticipantMailRecipient implements KonsultaMailRecipientInterface
{
    protected $userName;
    protected $userMailAddress;
    protected $programParticipationId;

    public function getFirstName(): string
    {
        
    }

    public function getMailAddress(): string
    {
        
    }

    public function getName(): string
    {
        
    }

}
