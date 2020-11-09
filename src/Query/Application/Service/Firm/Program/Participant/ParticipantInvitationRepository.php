<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Application\Service\ {
    Firm\Client\ProgramParticipation\ParticipantInvitationRepository as InterfaceForClient,
    Firm\Team\ProgramParticipation\ParticipantInvitationRepository as InterfaceForTeam,
    User\ProgramParticipation\ParticipantInvitationRepository as InterfaceForUser
};

interface ParticipantInvitationRepository extends InterfaceForClient, InterfaceForUser, InterfaceForTeam
{
    
}
