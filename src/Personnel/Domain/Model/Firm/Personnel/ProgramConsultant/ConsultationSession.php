<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\ {
    Personnel\ProgramConsultant,
    Program\ConsultationSetup,
    Program\Participant
};
use Resources\Domain\ValueObject\DateTimeInterval;

class ConsultationSession
{


    /**
     *
     * @var ProgramConsultant
     */
    protected $programConsultant;

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
     * @var DateTimeInterval
     */
    protected $startEndTime;

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

    function getProgramConsultant(): ProgramConsultant
    {
        return $this->programConsultant;
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
