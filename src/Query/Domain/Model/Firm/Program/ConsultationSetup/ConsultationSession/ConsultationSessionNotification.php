<?php

namespace Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;

use Query\Domain\ {
    Model\Firm\Program\ConsultationSetup\ConsultationSession,
    SharedModel\Notification
};

class ConsultationSessionNotification
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
     * @var Notification
     */
    protected $notification;

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
        return $this->notification->getMessage();
    }

}
