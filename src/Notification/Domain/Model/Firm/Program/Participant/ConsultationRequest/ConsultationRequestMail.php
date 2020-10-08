<?php

namespace Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest;

use Notification\Domain\{
    Model\Firm\Program\Participant\ConsultationRequest,
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

    public function __construct(
            ConsultationRequest $consultationRequest, string $id, string $senderMailAddress, string $senderName,
            string $subject, string $message, ?string $htmlMessage, string $recipientMailAddress, string $recipientName)
    {
//        $this->consultationRequest = $consultationRequest;
//        $this->id = $id;
//        $this->mail = new Mail($senderMailAddress, $senderName, $subject, $message, $htmlMessage, $recipientMailAddress,
//                $recipientName);
    }

}
