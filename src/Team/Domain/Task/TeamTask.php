<?php

namespace Team\Domain\Task;

use Team\Domain\Model\Team;

interface TeamTask
{

    public function execute(Team $team, $payload): void;
}
