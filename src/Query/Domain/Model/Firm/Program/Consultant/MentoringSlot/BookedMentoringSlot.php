<?php

namespace Query\Domain\Model\Firm\Program\Consultant\MentoringSlot;

use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\SharedModel\Mentoring;
use Query\Domain\SharedModel\Mentoring\MentorReport;
use Query\Domain\SharedModel\Mentoring\ParticipantReport;

class BookedMentoringSlot
{

    /**
     * 
     * @var MentoringSlot
     */
    protected $mentoringSlot;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var bool
     */
    protected $cancelled;

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var Mentoring
     */
    protected $mentoring;

    public function getMentoringSlot(): MentoringSlot
    {
        return $this->mentoringSlot;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCancelled(): bool
    {
        return $this->cancelled;
    }

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    protected function __construct()
    {
        
    }
    
    public function getParticipantReport(): ?ParticipantReport
    {
        return $this->mentoring->getParticipantReport();
    }

    public function getMentorReport(): ?MentorReport
    {
        return $this->mentoring->getMentorReport();
    }

}
