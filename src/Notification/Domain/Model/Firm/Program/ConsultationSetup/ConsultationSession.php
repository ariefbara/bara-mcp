<?php

namespace Notification\Domain\Model\Firm\Program\ConsultationSetup;

use Notification\Domain\Model\Firm\Program\ {
    Consultant,
    ConsultationSetup,
    ConsultationSetup\ConsultationSession\ConsultantFeedback,
    ConsultationSetup\ConsultationSession\ParticipantFeedback,
    Participant
};
use Resources\Domain\ValueObject\DateTimeInterval;

class ConsultationSession
{

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ConsultationSetup
     */
    protected $consultationSetup;

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

    protected function __construct()
    {
        ;
    }

}
