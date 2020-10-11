<?php

namespace Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;

use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Resources\Domain\Model\Mail;

class ConsultationSessionMail
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
     * @var Mail
     */
    protected $mail;

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

    public function getSenderMailAddress(): string
    {
        return $this->mail->getSenderMailAddress();
    }

    public function getSenderName(): string
    {
        return $this->mail->getSenderName();
    }

    public function getSubject(): string
    {
        return $this->mail->getSubject();
    }

    public function getMessage(): string
    {
        return $this->mail->getMessage();
    }

    public function getHtmlMessage(): ?string
    {
        return $this->mail->getHtmlMessage();
    }

}
