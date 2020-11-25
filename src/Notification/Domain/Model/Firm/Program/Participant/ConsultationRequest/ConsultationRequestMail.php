<?php

namespace Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest;

use Notification\Domain\{
    Model\Firm\Program\Participant\ConsultationRequest,
    SharedModel\Mail
};
use SharedContext\Domain\ValueObject\MailMessage;

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

    function __construct(
            ConsultationRequest $consultationRequest, string $id, $senderMailAddress, string $senderName,
            MailMessage $mailMessage, string $recipientMailAddress, string $recipientName)
    {
        $this->consultationRequest = $consultationRequest;
        $this->id = $id;
        $this->mail = new Mail($id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
    }

}
