<?php

namespace Query\Application\Service\Firm\Program\Participant\Activity;

use Query\Application\Service\ {
    Firm\Client\ProgramParticipation\Activity\InvitationRepository as InterfaceForClient,
    Firm\Team\ProgramParticipation\Activity\InvitationRepository as InterfaceForTeam,
    User\ProgramParticipation\Activity\InvitationRepository as InterfaceForUser
};

interface InvitationRepository extends InterfaceForClient, InterfaceForUser, InterfaceForTeam
{
    
}
