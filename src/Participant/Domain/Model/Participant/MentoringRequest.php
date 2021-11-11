<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\Participant;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use SharedContext\Domain\ValueObject\Schedule;
use SharedContext\Domain\ValueObject\ScheduleData;

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

    public function __construct(
            Participant $participant, string $id, ScheduleData $scheduleData, Consultant $mentor,
            ConsultationSetup $consultationSetup)
    {
//        $this->participant = $participant;
//        $this->id = $id;
//        $this->requestStatus = $requestStatus;
//        $this->schedule = $schedule;
//        $this->mentor = $mentor;
//        $this->consultationSetup = $consultationSetup;
    }

}
