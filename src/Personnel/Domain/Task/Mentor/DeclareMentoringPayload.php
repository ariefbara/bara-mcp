<?php

namespace Personnel\Domain\Task\Mentor;

use SharedContext\Domain\ValueObject\ScheduleData;

class DeclareMentoringPayload
{

    /**
     * 
     * @var string|null
     */
    protected $participantId;

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

    public function getParticipantId(): ?string
    {
        return $this->participantId;
    }

    public function getConsultationSetupId(): ?string
    {
        return $this->consultationSetupId;
    }

    public function getScheduleData(): ?ScheduleData
    {
        return $this->scheduleData;
    }

    public function __construct(?string $participantId, ?string $consultationSetupId, ?ScheduleData $scheduleData)
    {
        $this->participantId = $participantId;
        $this->consultationSetupId = $consultationSetupId;
        $this->scheduleData = $scheduleData;
    }

}
