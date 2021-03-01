<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\Model\Participant;

interface ManageableByParticipant
{
    public function isManageableByParticipant(Participant $participant): bool;
}
