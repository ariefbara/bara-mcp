<?php

namespace Participant\Application\Service\Firm\Program;

use Participant\{
    Application\Service\ClientParticipant\ConsultantRepository as InterfaceForClientParticipant,
    Application\Service\UserParticipant\ConsultantRepository as InterfaceForUserParticipant,
    Domain\DependencyModel\Firm\Program\Consultant
};

interface ConsultantRepository extends InterfaceForClientParticipant, InterfaceForUserParticipant
{

    public function ofId(string $consultantId): Consultant;
}
