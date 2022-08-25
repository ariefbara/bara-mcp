<?php

namespace Firm\Application\Listener;

use Firm\Domain\Model\Firm\Team;

interface TeamRepository
{

    public function ofId(string $id): Team;
}
