<?php

namespace Participant\Application\Service\Firm\Program;

use Participant\Application\Service\ {
    ClientParticipant\MissionRepository as InterfaceForClientParticipant,
    UserParticipant\MissionRepository as InterfaceForUserParticipant
};

interface MissionRepository extends InterfaceForClientParticipant, InterfaceForUserParticipant
{
    
}
