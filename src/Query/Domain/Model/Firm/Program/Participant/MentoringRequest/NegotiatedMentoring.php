<?php

namespace Query\Domain\Model\Firm\Program\Participant\MentoringRequest;

use Query\Domain\Model\Firm\Program\Participant\MentoringRequest;
use Query\Domain\SharedModel\Mentoring;
use Query\Domain\SharedModel\Mentoring\MentorReport;
use Query\Domain\SharedModel\Mentoring\ParticipantReport;

class NegotiatedMentoring
{

    /**
     * 
     * @var MentoringRequest
     */
    protected $mentoringRequest;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Mentoring
     */
    protected $mentoring;

    public function getMentoringRequest(): MentoringRequest
    {
        return $this->mentoringRequest;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMentoring(): Mentoring
    {
        return $this->mentoring;
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
