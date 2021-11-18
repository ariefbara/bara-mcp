<?php

namespace Firm\Domain\Task\Dependency\Firm;

use Firm\Domain\Model\Firm\Team;

interface TeamRepository
{

    public function nextIdentity(): string;

    public function add(Team $team): void;

    public function ofId(string $id): Team;
}
