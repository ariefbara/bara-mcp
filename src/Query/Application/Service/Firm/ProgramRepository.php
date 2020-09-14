<?php

namespace Query\Application\Service\Firm;

use Query\ {
    Application\Service\User\ProgramRepository as InterfaceForUser,
    Domain\Model\Firm\Program
};

interface ProgramRepository extends InterfaceForUser
{

    public function ofId(string $firmId, string $programId): Program;

    public function all(string $firmId, int $page, int $pageSize, ?string $participantType);
}
