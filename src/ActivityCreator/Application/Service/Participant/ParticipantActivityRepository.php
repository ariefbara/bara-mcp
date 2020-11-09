<?php

namespace ActivityCreator\Application\Service\Participant;

use ActivityCreator\Application\Service\ {
    ClientParticipant\ParticipantActivityRepository as InterfaceForClient,
    TeamMember\ParticipantActivityRepository as InterfaceForTeamMember,
    UserParticipant\ParticipantActivityRepository as InterfaceForUser
};

interface ParticipantActivityRepository extends InterfaceForClient, InterfaceForUser, InterfaceForTeamMember
{
    
}
