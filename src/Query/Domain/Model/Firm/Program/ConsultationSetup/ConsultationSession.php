<?php

namespace Query\Domain\Model\Firm\Program\ConsultationSetup;

use Query\Domain\Model\Firm\Program\{
    Consultant,
    ConsultationSetup,
    ConsultationSetup\ConsultationSession\ConsultantFeedback,
    ConsultationSetup\ConsultationSession\ParticipantFeedback,
    Participant
};
use Resources\Domain\ValueObject\DateTimeInterval;

class ConsultationSession
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
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     *
     * @var Consultant
     */
    protected $consultant;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;

    /**
     *
     * @var ParticipantFeedback||null
     */
    protected $participantFeedback = null;

    /**
     *
     * @var ConsultantFeedback||null
     */
    protected $consultantFeedback = null;

    function getParticipant(): Participant
    {
        return $this->participant;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getConsultationSetup(): ConsultationSetup
    {
        return $this->consultationSetup;
    }

    function getConsultant(): Consultant
    {
        return $this->consultant;
    }

    function getParticipantFeedback(): ?ParticipantFeedback
    {
        return $this->participantFeedback;
    }

    function getConsultantFeedback(): ?ConsultantFeedback
    {
        return $this->consultantFeedback;
    }

    protected function __construct()
    {
        ;
    }

    function getStartTime(): string
    {
        return $this->startEndTime->getStartTime()->format("Y-m-d H:i:s");
    }

    function getEndTime(): string
    {
        return $this->startEndTime->getEndTime()->format("Y-m-d H:i:s");
    }

    public function hasConsultantFeedback(): bool
    {
        return isset($this->consultantFeedback);
    }

    public function hasParticipantFeedback(): bool
    {
        return isset($this->participantFeedback);
    }

}
