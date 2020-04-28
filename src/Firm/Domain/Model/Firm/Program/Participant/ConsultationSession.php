<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use Firm\Domain\Model\Firm\Program\ {
    Consultant,
    ConsultationSetup,
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

//    protected $participantConsultationSetupReport;


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

    function getStartTimeString(): string
    {
        return $this->startEndTime->getStartTime()->format('Y-m-d H:i:s');
    }

    function getEndTimeString(): string
    {
        return $this->startEndTime->getEndTime()->format('Y-m-d H:i:s');
    }

    protected function __construct()
    {
        ;
    }
}
