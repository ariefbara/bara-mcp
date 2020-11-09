<?php

namespace ActivityCreator\Domain\Model\Activity;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Manager,
    service\ActivityDataProvider
};

class ManagerInvitation
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
     * @var Manager
     */
    protected $manager;

    function __construct(Invitation $invitation, string $id, Manager $manager)
    {
        $this->invitation = $invitation;
        $this->id = $id;
        $this->manager = $manager;
    }
    
    public function removeIfNotApprearInList(ActivityDataProvider $activityDataProvider): void
    {
        if (!$activityDataProvider->containManager($this->manager)) {
            $this->invitation->remove();
        }
    }
    
    public function managerEquals(Manager $manager): bool
    {
        return $this->manager === $manager;
    }

}
