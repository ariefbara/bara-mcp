<?php

namespace ActivityCreator\Domain\Model\Activity\Invitee;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Manager,
    Model\Activity\Invitee,
    Model\CanReceiveInvitation
};

class ManagerInvitee
{

    /**
     *
     * @var Invitee
     */
    protected $invitee;

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

    function __construct(Invitee $invitee, string $id, Manager $manager)
    {
        $this->invitee = $invitee;
        $this->id = $id;
        $this->manager = $manager;
    }
    
    public function managerEquals(CanReceiveInvitation $manager): bool
    {
        return $this->manager === $manager;
    }

}
