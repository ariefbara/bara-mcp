<?php

namespace Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;

use Notification\Domain\{
    Model\Firm\Program\Participant\ConsultationSession,
    SharedModel\Mail
};
use SharedContext\Domain\ValueObject\MailMessage;

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

    function __construct(
            ConsultationSession $consultationSession, string $id, string $senderMailAddress, string $senderName,
            MailMessage $mailMessage, string $recipientMailAddress, string $recipientName)
    {
        $this->consultationSession = $consultationSession;
        $this->id = $id;
        $this->mail = new Mail($id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
    }

}
