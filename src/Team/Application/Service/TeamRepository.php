<?php

namespace Team\Application\Service;

use Team\Domain\Model\Team;

interface TeamRepository
{

    public function nextIdentity(): string;

    public function add(Team $team): void;

    public function isNameAvailable(string $firmId, string $name): bool;
}
