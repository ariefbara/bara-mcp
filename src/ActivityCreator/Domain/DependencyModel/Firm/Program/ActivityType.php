<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Program;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\ActivityType\ActivityParticipant,
    Model\Activity,
    service\ActivityDataProvider
};
use Doctrine\Common\Collections\ArrayCollection;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class ActivityType
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ArrayCollection
     */
    protected $participants;

    protected function __construct()
    {
        
    }
    
    public function belongsToProgram(Program $program): bool
    {
        return $this->program === $program;
    }
    
    public function canBeInitiatedBy(ActivityParticipantType $activityParticipantType): bool
    {
        $p = function (ActivityParticipant $activityParticipant) use($activityParticipantType){
            return $activityParticipant->canInitiateAndTypeEquals($activityParticipantType);
        };
        return !empty($this->participants->filter($p)->count());
    }
    
    public function addInviteesToActivity(Activity $activity, ActivityDataProvider $activityDataProvider): void
    {
        foreach ($this->participants->getIterator() as $participant) {
            $participant->addInviteesToActivity($activity, $activityDataProvider);
        }
    }

}
