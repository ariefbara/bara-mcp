<?php

namespace Participant\Domain\Task\Participant;

use SharedContext\Domain\ValueObject\ScheduleData;

class DeclareMentoringPayload
{

    /**
     * 
     * @var string|null
     */
    protected $mentorId;

    /**
     * 
     * @var string|null
     */
    protected $consultationSetupId;

    /**
     * 
     * @var ScheduleData|null
     */
    protected $scheduleData;

    public function getMentorId(): ?string
    {
        return $this->mentorId;
    }

    public function getConsultationSetupId(): ?string
    {
        return $this->consultationSetupId;
    }

    public function getScheduleData(): ?ScheduleData
    {
        return $this->scheduleData;
    }

    public function __construct(?string $mentorId, ?string $consultationSetupId, ?ScheduleData $scheduleData)
    {
        $this->mentorId = $mentorId;
        $this->consultationSetupId = $consultationSetupId;
        $this->scheduleData = $scheduleData;
    }

}
