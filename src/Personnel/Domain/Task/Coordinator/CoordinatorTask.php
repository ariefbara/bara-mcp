<?php

namespace Personnel\Domain\Task\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator;

interface CoordinatorTask
{

    public function execute(Coordinator $coordinator, $payload): void;
}
