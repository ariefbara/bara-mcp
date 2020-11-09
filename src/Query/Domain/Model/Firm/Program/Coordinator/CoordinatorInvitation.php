<?php

namespace Query\Domain\Model\Firm\Program\Coordinator;

use Query\Domain\Model\Firm\Program\ {
    Activity,
    Activity\Invitation,
    Coordinator
};

class CoordinatorInvitation
{

    /**
     *
     * @var Coordinator
     */
    protected $coordinator;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Invitation
     */
    protected $invitation;
    
    function getCoordinator(): Coordinator
    {
        return $this->coordinator;
    }

    function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }
    
    function getActivity(): Activity
    {
        return $this->invitation->getActivity();
    }

    function willAttend(): ?bool
    {
        return $this->invitation->willAttend();
    }

    function isAttended(): ?bool
    {
        return $this->invitation->isAttended();
    }

    function isRemoved(): bool
    {
        return $this->invitation->isRemoved();
    }

}
