<?php

namespace Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest;

use Notification\Domain\ {
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
}
