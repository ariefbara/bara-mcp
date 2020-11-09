<?php

namespace Query\Application\Service\Firm\Program\Activity;

use Query\Application\Service\Firm\ {
    Manager\Activity\InvitationRepository as InterfaceForManager,
    Personnel\ProgramConsultant\Activity\InvitationRepository as InterfaceForConsultant,
    Personnel\ProgramCoordinator\Activity\InvitationRepository as InterfaceForCoordinator,
    Program\Participant\Activity\InvitationRepository as InterfaceForParticipant
};

interface InvitationRepository extends InterfaceForManager, InterfaceForCoordinator, InterfaceForConsultant, InterfaceForParticipant
{
    
}
