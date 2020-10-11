<?php

namespace Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;

use Query\Domain\ {
    Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    SharedModel\Notification
};

class ConsultationRequestNotification
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
     * @var Notification
     */
    protected $notification;

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
        return $this->notification->getMessage();
    }

}
