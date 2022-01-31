<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use DateTimeImmutable;

class MentoringSlotSetting
{

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $startTime;

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $endTime;

    /**
     * 
     * @var int|null
     */
    protected $capacity;

    public function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): ?DateTimeImmutable
    {
        return $this->endTime;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function __construct(?DateTimeImmutable $startTime, ?DateTimeImmutable $endTime, ?int $capacity)
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->capacity = $capacity;
    }

}
