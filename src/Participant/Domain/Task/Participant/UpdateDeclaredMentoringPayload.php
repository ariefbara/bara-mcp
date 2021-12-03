<?php

namespace Participant\Domain\Task\Participant;

use SharedContext\Domain\ValueObject\ScheduleData;

class UpdateDeclaredMentoringPayload
{

    /**
     * 
     * @var string|null
     */
    protected $id;

    /**
     * 
     * @var ScheduleData|null
     */
    protected $scheduleData;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getScheduleData(): ?ScheduleData
    {
        return $this->scheduleData;
    }

    public function __construct(?string $id, ?ScheduleData $scheduleData)
    {
        $this->id = $id;
        $this->scheduleData = $scheduleData;
    }

}
