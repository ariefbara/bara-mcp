<?php

namespace Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;

use Query\Domain\ {
    Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    SharedModel\ActivityLog
};

class ConsultationRequestActivityLog
{

    /**
     *
     * @var ConsultationRequest
     */
    protected $consultationRequest;

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
    
    public function getConsultationRequest(): ConsultationRequest
    {
        return $this->consultationRequest;
    }

    public function getId(): string
    {
        return $this->id;
    }
        
    protected function __construct()
    {
        ;
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
