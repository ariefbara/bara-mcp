<?php

namespace Participant\Application\Service\Firm\Program;

use Participant\Application\Service\ {
    ClientParticipant\ConsultantRepository as InterfaceForClientParticipant,
    UserParticipant\ConsultantRepository as InterfaceForUserParticipant
};

interface ConsultantRepository extends InterfaceForClientParticipant, InterfaceForUserParticipant
{

}
