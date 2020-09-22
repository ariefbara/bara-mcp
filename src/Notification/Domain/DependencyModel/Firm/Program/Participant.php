<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\ {
    Client\ClientParticipant,
    Program,
    Program\Participant\ParticipantMailNotification
};
use Query\Domain\Model\User\UserParticipant;

class Participant
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
     * @var bool
     */
    protected $active = true;

    /**
     *
     * @var ClientParticipant||null
     */
    protected $clientParticipant;

    /**
     *
     * @var UserParticipant||null
     */
    protected $userParticipant;

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        ;
    }
    
    public function createMailNotification(): ParticipantMailNotification
    {
        return new ParticipantMailNotification($this);
    }
    
    public function getParticipantName(): string
    {
        
    }
    
/*
    public function getFirmWhitelableInfo(): FirmWhitelableInfo
    {
        return $this->program->getFirmWhitelableInfo();
    }

    public function getName(): string
    {
        if (!empty($this->clientParticipant)) {
            return $this->clientParticipant->getClientName();
        }
        return $this->userParticipant->getUserName();
    }

    public function getProgramId(): string
    {
        return $this->program->getId();
    }
    
    public function getMailRecipient(): Recipient
    {
    }
    
    public function getProgramParticipationId(): string
    {
        
    }
 * 
 */

}
