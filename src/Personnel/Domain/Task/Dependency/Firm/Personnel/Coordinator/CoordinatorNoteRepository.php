<?php

namespace Personnel\Domain\Task\Dependency\Firm\Personnel\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator\CoordinatorNote;

interface CoordinatorNoteRepository
{

    public function nextIdentity(): string;

    public function add(CoordinatorNote $coordinatorNote): void;

    public function ofId(string $id): CoordinatorNote;
}
