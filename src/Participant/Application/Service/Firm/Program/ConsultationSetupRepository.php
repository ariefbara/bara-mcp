<?php

namespace Participant\Application\Service\Firm\Program;

use Participant\ {
    Application\Service\ClientParticipant\ConsultationSetupRepository as InterfaceForClientParticipant,
    Application\Service\UserParticipant\ConsultationSetupRepository as InterfaceForUserParticipant,
    Domain\DependencyModel\Firm\Program\ConsultationSetup
};

interface ConsultationSetupRepository extends InterfaceForClientParticipant, InterfaceForUserParticipant
{
    public function ofId(string $consultationSetupId): ConsultationSetup;
}
