<?php

namespace Participant\Domain\Task\Dependency\Firm\Program\Participant;

use Participant\Domain\Model\Participant\Task;

interface TaskRepository
{

    public function ofId(string $id): Task;
}
