<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\ConsultationSetup;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\SharedModel\Mentoring;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;
use SharedContext\Domain\ValueObject\Schedule;

class DeclaredMentoring
{

    /**
     * 
     * @var Consultant
     */
    protected $mentor;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     * 
     * @var DeclaredMentoringStatus
     */
    protected $declaredStatus;

    /**
     * 
     * @var Schedule
     */
    protected $schedule;

    /**
     * 
     * @var Mentoring
     */
    protected $mentoring;

    public function getMentor(): Consultant
    {
        return $this->mentor;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getConsultationSetup(): ConsultationSetup
    {
        return $this->consultationSetup;
    }

    public function getMentoring(): Mentoring
    {
        return $this->mentoring;
    }

    protected function __construct()
    {
        
    }

    public function getDeclaredStatusDisplayValue(): string
    {
        return $this->declaredStatus->getDisplayValue();
    }

    public function getStartTimeString(): ?string
    {
        return $this->schedule->getStartTimeString();
    }

    public function getEndTimeString(): ?string
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

}
