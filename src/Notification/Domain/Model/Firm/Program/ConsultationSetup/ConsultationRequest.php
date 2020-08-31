<?php

namespace Notification\Domain\Model\Firm\Program\ConsultationSetup;

use Notification\Domain\Model\Firm\Program\{
    Consultant,
    ConsultationSetup,
    Participant
};
use Resources\Domain\ValueObject\DateTimeInterval;

class ConsultationRequest
{

    /**
     *
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var Consultant
     */
    protected $consultant;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;

    /**
     *
     * @var bool
     */
    protected $concluded;

    /**
     *
     * @var string||null
     */
    protected $status;
    
    protected function __construct()
    {
        ;
    }
}
