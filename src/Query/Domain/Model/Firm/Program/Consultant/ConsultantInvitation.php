<?php

namespace Query\Domain\Model\Firm\Program\Consultant;

use Query\Domain\Model\Firm\Program\ {
    Activity,
    Activity\Invitation,
    Consultant
};

class ConsultantInvitation
{

    /**
     *
     * @var Consultant
     */
    protected $consultant;

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

    function getConsultant(): Consultant
    {
        return $this->consultant;
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
