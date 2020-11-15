<?php

namespace Query\Application\Service\Firm\Program\Activity;

use Query\Application\Service\Firm\ {
    Manager\Activity\InviteeRepository as InterfaceForManager,
    Personnel\ProgramConsultant\Activity\InvitationRepository as InterfaceForConsultant,
    Personnel\ProgramCoordinator\Activity\InvitationRepository as InterfaceForCoordinator,
    Program\Participant\Activity\InvitationRepository as InterfaceForParticipant
};

interface InviteeRepository extends InterfaceForManager, InterfaceForCoordinator, InterfaceForConsultant, InterfaceForParticipant
{
    
}
