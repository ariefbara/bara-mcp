<?php

namespace Query\Domain\Model\Firm\Program\Activity;

use Query\Domain\Model\Firm\ {
    Manager\ManagerInvitation,
    Program\Activity,
    Program\Consultant\ConsultantInvitation,
    Program\Coordinator\CoordinatorInvitation,
    Program\Participant\ParticipantInvitation
};

class Invitation
{

    /**
     *
     * @var Activity
     */
    protected $activity;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool|null
     */
    protected $willAttend;

    /**
     *
     * @var bool|null
     */
    protected $attended;

    /**
     *
     * @var bool
     */
    protected $removed;

    /**
     *
     * @var ManagerInvitation|null
     */
    protected $managerInvitation;

    /**
     *
     * @var CoordinatorInvitation|null
     */
    protected $coordinatorInvitation;

    /**
     *
     * @var ConsultantInvitation|null
     */
    protected $consultantInvitation;

    /**
     *
     * @var ParticipantInvitation|null
     */
    protected $participantInvitation;

    function getActivity(): Activity
    {
        return $this->activity;
    }

    function getId(): string
    {
        return $this->id;
    }

    function willAttend(): ?bool
    {
        return $this->willAttend;
    }

    function isAttended(): ?bool
    {
        return $this->attended;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        
    }

    function getManagerInvitation(): ?ManagerInvitation
    {
        return $this->managerInvitation;
    }

    function getCoordinatorInvitation(): ?CoordinatorInvitation
    {
        return $this->coordinatorInvitation;
    }

    function getConsultantInvitation(): ?ConsultantInvitation
    {
        return $this->consultantInvitation;
    }

    function getParticipantInvitation(): ?ParticipantInvitation
    {
        return $this->participantInvitation;
    }

}
