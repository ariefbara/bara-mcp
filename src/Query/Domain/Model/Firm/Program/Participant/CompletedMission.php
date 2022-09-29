<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Program\{
    Mission,
    Participant
};

class CompletedMission
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
     * @var Mission
     */
    protected $mission;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $completedTime;

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMission(): Mission
    {
        return $this->mission;
    }

    public function getCompletedTime(): DateTimeImmutable
    {
        return $this->completedTime;
    }

    public function getCompletedTimeString(): string
    {
        return $this->completedTime->format("Y-m-d H:i:s");
    }

    protected function __construct()
    {
        
    }

}
