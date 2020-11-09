<?php

namespace Query\Application\Service\Firm\Program\Activity;

use Query\ {
    Application\Service\Firm\Personnel\ProgramConsultant\Activity\InvitationRepository as InterfaceForConsultant,
    Application\Service\Firm\Personnel\ProgramCoordinator\Activity\InvitationRepository as InterfaceForCoordinator,
    Application\Service\Firm\Program\Participant\Activity\InvitationRepository as InterfaceForParticipant,
    Domain\Model\Firm\Manager\Activity\InvitationRepository as InterfaceForManager
};

interface InvitationRepository extends InterfaceForManager, InterfaceForCoordinator, InterfaceForConsultant, InterfaceForParticipant
{
    
}
