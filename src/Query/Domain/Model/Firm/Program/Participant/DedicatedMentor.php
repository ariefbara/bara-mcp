<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\Participant;

class DedicatedMentor
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Consultant
     */
    protected $consultant;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $modifiedTime;

    /**
     * 
     * @var bool
     */
    protected $cancelled;

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getConsultant(): Consultant
    {
        return $this->consultant;
    }

    public function getModifiedTimeString(): string
    {
        return $this->modifiedTime->format('Y-m-d H:i:s');
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }
    
    protected function __construct()
    {
        
    }

}
