<?php

namespace Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;

use Notification\Domain\ {
    Model\Firm\Program\Participant\ConsultationSession,
    SharedModel\Mail
};

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

    public function __construct(
            ConsultationSession $consultationSession, string $id, string $senderMailAddress, string $senderName,
            string $subject, string $message, ?string $htmlMessage, string $recipientMailAddress, string $recipientName)
    {
        $this->consultationSession = $consultationSession;
        $this->id = $id;
        $this->mail = new Mail(
                $id, $senderMailAddress, $senderName, $subject, $message, $htmlMessage, $recipientMailAddress,
                $recipientName);
    }
}
