<?php

namespace Query\Domain\SharedModel;

use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestNotification;

class Notification
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $message;

    /**
     *
     * @var ConsultationRequestNotification|null
     */
    protected $consultationRequestNotification;

    public function getId(): string
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getConsultationRequestNotification(): ?ConsultationRequestNotification
    {
        return $this->consultationRequestNotification;
    }

    protected function __construct()
    {
        ;
    }

}
