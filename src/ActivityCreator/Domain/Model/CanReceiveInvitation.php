<?php

namespace ActivityCreator\Domain\Model;

use ActivityCreator\Domain\{
    DependencyModel\Firm\Program,
    Model\Activity\Invitee
};

interface CanReceiveInvitation
{

    public function registerAsInviteeRecipient(Invitee $invitee): void;

    public function canInvolvedInProgram(Program $program): bool;
}
