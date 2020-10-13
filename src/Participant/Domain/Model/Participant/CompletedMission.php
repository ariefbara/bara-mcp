<?php

namespace Participant\Domain\Model\Participant;

use DateTimeImmutable;
use Participant\Domain\ {
    DependencyModel\Firm\Program\Mission,
    Model\Participant
};
use Resources\DateTimeImmutableBuilder;

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
    
    public function __construct(Participant $participant, string $id, Mission $mission)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->mission = $mission;
        $this->completedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }
    
    public function correspondWithMission(Mission $mission): bool
    {
        return $this->mission === $mission;
    }


}
