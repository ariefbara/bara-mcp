<?php

namespace Query\Application\Service\Firm\Program;

use Query\ {
    Application\Service\Firm\Client\ProgramParticipation\MissionRepository as InterfaceForClient,
    Application\Service\User\ProgramParticipation\MissionRepository as InterfaceForUser,
    Domain\Model\Firm\Program\Mission
};

interface MissionRepository extends InterfaceForClient, InterfaceForUser
{

    public function ofId(string $firmId, string $programId, string $missionId): Mission;

    public function all(string $firmId, string $programId, int $page, int $pageSize, ?string $position);
    
}
