<?php

namespace ActivityInvitee\Application\Service\Participant;

use ActivityInvitee\Application\Service\ {
    ClientParticipant\ActivityInvitationRepository as InterfaceForClient,
    TeamMember\ActivityInvitationRepository as InterfaceForTeamMember,
    UserParticipant\ActivityInvitationRepository as InterfaceForUser
};

interface ActivityInvitationRepository extends InterfaceForClient, InterfaceForTeamMember, InterfaceForUser
{
    
}
