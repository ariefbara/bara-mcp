<?php

namespace Query\Domain\Model\Firm\Personnel;

use DateTimeImmutable;
use Query\Domain\Model\Firm\{
    Personnel,
    Program\Participant\ConsultationRequest,
    Program\Participant\ConsultationSession
};

class PersonnelNotification
{

    /**
     *
     * @var Personnel
     */
    protected $personnel;

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
     * @var bool
     */
    protected $read = false;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $notifiedTime;

    /**
     *
     * @var ConsultationRequest||null
     */
    protected $consultationRequest = null;

    /**
     *
     * @var ConsultationSession||null
     */
    protected $consultationSession = null;

    function getPersonnel(): Personnel
    {
        return $this->personnel;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getMessage(): string
    {
        return $this->message;
    }

    function isRead(): bool
    {
        return $this->read;
    }

    function getNotifiedTimeString(): string
    {
        return $this->notifiedTime->format('Y-m-d H:i:s');
    }

    function getConsultationRequest(): ?ConsultationRequest
    {
        return $this->consultationRequest;
    }

    function getConsultationSession(): ?ConsultationSession
    {
        return $this->consultationSession;
    }

    protected function __construct()
    {
        ;
    }

}
