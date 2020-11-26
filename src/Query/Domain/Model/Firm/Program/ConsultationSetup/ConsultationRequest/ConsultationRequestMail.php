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

    protected function __construct()
    {
        ;
    }

}
