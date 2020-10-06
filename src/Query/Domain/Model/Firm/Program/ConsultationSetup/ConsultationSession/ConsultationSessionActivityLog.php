<?php

namespace Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;

use Query\Domain\ {
    Model\Firm\Program\ConsultationSetup\ConsultationSession,
    SharedModel\ActivityLog
};

class ConsultationSessionActivityLog
{

    /**
     *
     * @var ConsultationSession
     */
    protected $consultationSession;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityLog
     */
    protected $activityLog;

    public function getConsultationSession(): ConsultationSession
    {
        return $this->consultationSession;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function getMessage(): string
    {
        return $this->activityLog->getMessage();
    }

    public function getOccuredTimeString(): string
    {
        return $this->activityLog->getOccuredTimeString();
    }

}
