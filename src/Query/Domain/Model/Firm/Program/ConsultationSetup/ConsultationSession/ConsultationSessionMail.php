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

    protected function __construct()
    {
        
    }

}
