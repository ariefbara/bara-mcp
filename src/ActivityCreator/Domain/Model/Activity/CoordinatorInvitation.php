<?php

namespace ActivityCreator\Domain\Model\Activity;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Personnel\Coordinator,
    service\ActivityDataProvider
};

class CoordinatorInvitation
{

    /**
     *
     * @var Invitation
     */
    protected $invitation;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Coordinator
     */
    protected $coordinator;

    function __construct(Invitation $invitation, string $id, Coordinator $coordinator)
    {
        $this->invitation = $invitation;
        $this->id = $id;
        $this->coordinator = $coordinator;
    }
    
    public function removeIfNotApprearInList(ActivityDataProvider $activityDataProvider): void
    {
        if (!$activityDataProvider->containCoordinator($this->coordinator)) {
            $this->invitation->remove();
        }
    }
    
    public function CoordinatorEquals(Coordinator $coordinator): bool
    {
        return $this->coordinator === $coordinator;
    }

}
