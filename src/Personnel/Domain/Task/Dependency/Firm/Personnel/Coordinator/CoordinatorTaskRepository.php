<?php

namespace Personnel\Domain\Task\Dependency\Firm\Personnel\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator\CoordinatorTask;


interface CoordinatorTaskRepository
{

    public function nextIdentity(): string;

    public function add(CoordinatorTask $coordinatorTask): void;

    public function ofId(string $id): CoordinatorTask;
}
