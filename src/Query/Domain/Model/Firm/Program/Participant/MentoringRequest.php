<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\ConsultationSetup;
use Query\Domain\Model\Firm\Program\Participant;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use SharedContext\Domain\ValueObject\Schedule;

class MentoringRequest
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
     * @var MentoringRequestStatus
     */
    protected $requestStatus;
    /**
     * 
     * @var Schedule
     */
    protected $schedule;
    /**
     * 
     * @var Consultant
     */
    protected $mentor;
    /**
     * 
     * @var ConsultationSetup
     */
    protected $consultationSetup;
}
