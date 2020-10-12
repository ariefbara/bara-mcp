<?php

namespace Query\Domain\SharedModel;

use Query\Domain\Model\Firm\Program\{
    ConsultationSetup\ConsultationRequest\ConsultationRequestNotification,
    ConsultationSetup\ConsultationSession\ConsultationSessionNotification,
    Participant\Worksheet\Comment\CommentNotification
};

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

    /**
     *
     * @var ConsultationSessionNotification|null
     */
    protected $consultationSessionNotification;

    /**
     *
     * @var CommentNotification|null
     */
    protected $commentNotification;

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

    public function getConsultationSessionNotification(): ?ConsultationSessionNotification
    {
        return $this->consultationSessionNotification;
    }

    public function getCommentNotification(): ?CommentNotification
    {
        return $this->commentNotification;
    }

    protected function __construct()
    {
        ;
    }

}
