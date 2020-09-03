<?php

namespace Participant\Application\Service\Firm\Program;

use Participant\Application\Service\ {
    ClientParticipant\ConsultationSetupRepository as InterfaceForClientParticipant,
    UserParticipant\ConsultationSetupRepository as InterfaceForUserParticipant
};

interface ConsultationSetupRepository extends InterfaceForClientParticipant, InterfaceForUserParticipant
{

}
