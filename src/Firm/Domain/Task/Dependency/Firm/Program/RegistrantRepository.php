<?php

namespace Firm\Domain\Task\Dependency\Firm\Program;

use Firm\Domain\Model\Firm\Program\Registrant;

interface RegistrantRepository
{

    public function aRegistrantOfId(string $id): Registrant;
}
