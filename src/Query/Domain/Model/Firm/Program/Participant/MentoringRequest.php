<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\ConsultationSetup;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest\NegotiatedMentoring;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use SharedContext\Domain\ValueObject\Schedule;

class MentoringRequest
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
     * @var MentoringRequestStatus
     */
    protected $requestStatus;

    /**
     * 
     * @var Schedule
     */
    protected $schedule;

    /**
     * 
     * @var Consultant
     */
    protected $mentor;

    /**
     * 
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     * 
     * @var NegotiatedMentoring|null
     */
    protected $negotiatedMentoring;

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMentor(): Consultant
    {
        return $this->mentor;
    }

    public function getConsultationSetup(): ConsultationSetup
    {
        return $this->consultationSetup;
    }

    public function getNegotiatedMentoring(): ?NegotiatedMentoring
    {
        return $this->negotiatedMentoring;
    }

    protected function __construct()
    {
        
    }
    
    public function getStartTimeString(): string
    {
        return $this->schedule->getStartTimeString();
    }

    public function getEndTimeString(): string
    {
        return $this->schedule->getEndTimeString();
    }

    public function getMediaType(): ?string
    {
        return $this->schedule->getMediaType();
    }

    public function getLocation(): ?string
    {
        return $this->schedule->getLocation();
    }
    
    public function getRequestStatusString(): string
    {
        return $this->requestStatus->getDisplayValue();
    }

}
