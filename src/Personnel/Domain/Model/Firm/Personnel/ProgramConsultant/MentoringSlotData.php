<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use SharedContext\Domain\ValueObject\ScheduleData;

class MentoringSlotData
{

    /**
     * 
     * @var ScheduleData|null
     */
    protected $scheduleData;

    /**
     * 
     * @var int|null
     */
    protected $capacity;

    public function getScheduleData(): ?ScheduleData
    {
        return $this->scheduleData;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function __construct(?ScheduleData $scheduleData, ?int $capacity)
    {
        $this->scheduleData = $scheduleData;
        $this->capacity = $capacity;
    }

}
