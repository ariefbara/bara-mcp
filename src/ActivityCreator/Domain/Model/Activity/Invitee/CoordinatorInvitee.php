<?php

namespace ActivityCreator\Domain\Model\Activity\Invitee;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Personnel\Coordinator,
    Model\Activity\Invitee,
    Model\CanReceiveInvitation
};

class CoordinatorInvitee
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
     * @var Coordinator
     */
    protected $coordinator;

    function __construct(Invitee $invitee, string $id, Coordinator $coordinator)
    {
        $this->invitee = $invitee;
        $this->id = $id;
        $this->coordinator = $coordinator;
    }
    
    public function coordinatorEquals(CanReceiveInvitation $coordinator): bool
    {
        return $this->coordinator === $coordinator;
    }

}
