<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\ {
    Consultant,
    ConsultationSetup,
    Participant,
    Participant\ConsultationSession\ConsultantFeedback,
    Participant\ConsultationSession\ParticipantFeedback
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

}
