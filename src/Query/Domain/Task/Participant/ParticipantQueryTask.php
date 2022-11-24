<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\Participant;

interface ParticipantQueryTask
{

    public function execute(Participant $participant, $payload): void;
}
