<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Coordinator;

use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorTask;

interface CoordinatorTaskRepository
{

    public function aCoordinatorTaskDetailForParticipant(string $participantId, string $id): CoordinatorTask;

    public function aCoordinatorTaskInProgram(string $programId, string $id): CoordinatorTask;
}
