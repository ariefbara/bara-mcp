<?php

namespace ActivityCreator\Domain\Model;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program\Participant,
    service\ActivityDataProvider
};
use Resources\Domain\Model\EntityContainEvents;

class ParticipantActivity extends EntityContainEvents
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
     * @var Activity
     */
    protected $activity;

    function __construct(Participant $participant, string $id, Activity $activity)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->activity = $activity;
    }

    public function update(ActivityDataProvider $activityDataProvider): void
    {
        $this->activity->update($activityDataProvider);
    }

    public function belongsToTeam(string $teamId): bool
    {
        return $this->participant->belongsToTeam($teamId);
    }

}
