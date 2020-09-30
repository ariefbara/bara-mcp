<?php

namespace Participant\Application\Service\Firm\Program;

use Participant\ {
    Application\Service\ClientParticipant\MissionRepository as InterfaceForClientParticipant,
    Application\Service\UserParticipant\MissionRepository as InterfaceForUserParticipant,
    Domain\DependencyModel\Firm\Program\Mission
};

interface MissionRepository extends InterfaceForClientParticipant, InterfaceForUserParticipant
{
    public function ofId(string $missionId): Mission;
}
