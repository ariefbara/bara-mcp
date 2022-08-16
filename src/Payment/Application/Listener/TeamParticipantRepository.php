<?php

namespace Payment\Application\Listener;

use Payment\Domain\Model\Firm\Team\TeamParticipant;

interface TeamParticipantRepository
{

    public function ofId(string $id): ?TeamParticipant;

    public function update(): void;
}
