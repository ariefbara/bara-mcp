<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Application\Service\ {
    Firm\Client\ProgramParticipation\ParticipantActivityRepository as InterfaceForClient,
    Firm\Team\ProgramParticipation\ParticipantActivityRepository as InterfaceForTeam,
    User\ProgramParticipation\ParticipantActivityRepository as InterfaceForUser
};

interface ParticipantActivityRepository extends InterfaceForClient, InterfaceForUser, InterfaceForTeam
{
    
}
