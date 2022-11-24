<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant;

interface ParticipantTask
{

    public function execute(Participant $participant, $payload): void;
}
