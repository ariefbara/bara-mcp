<?php

namespace Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;

use Query\Domain\{
    Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    SharedModel\Mail
};

class ConsultationRequestMail
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
     * @var Mail
     */
    protected $mail;

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
