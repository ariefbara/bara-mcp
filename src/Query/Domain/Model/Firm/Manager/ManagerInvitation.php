<?php

namespace Query\Domain\Model\Firm\Manager;

use Query\Domain\Model\Firm\ {
    Manager,
    Program\Activity,
    Program\Activity\Invitation
};

class ManagerInvitation
{

    /**
     *
     * @var Manager
     */
    protected $manager;

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

    function getManager(): Manager
    {
        return $this->manager;
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
