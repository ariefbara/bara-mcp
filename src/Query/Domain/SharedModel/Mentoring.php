<?php

namespace Query\Domain\SharedModel;

use SharedContext\Domain\Model\Mentoring\MentorReport;
use SharedContext\Domain\Model\Mentoring\ParticipantReport;

class Mentoring
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var ParticipantReport|null
     */
    protected $participantReport;

    /**
     * 
     * @var MentorReport|null
     */
    protected $mentorReport;
    
    public function getId(): string
    {
        return $this->id;
    }

    public function getParticipantReport(): ?ParticipantReport
    {
        return $this->participantReport;
    }

    public function getMentorReport(): ?MentorReport
    {
        return $this->mentorReport;
    }

    
    protected function __construct()
    {
        
    }

}
