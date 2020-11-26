<?php

namespace Query\Application\Service\Firm\Program\Activity;

use Query\{
    Application\Service\Firm\Manager\Activity\InviteeRepository as InterfaceForManager,
    Application\Service\Firm\Personnel\ProgramConsultant\Activity\InvitationRepository as InterfaceForConsultant,
    Application\Service\Firm\Personnel\ProgramCoordinator\Activity\InvitationRepository as InterfaceForCoordinator,
    Application\Service\Firm\Program\Participant\Activity\InvitationRepository as InterfaceForParticipant,
    Domain\Model\Firm\Program\Activity\Invitee
};

interface InviteeRepository extends InterfaceForManager, InterfaceForCoordinator, InterfaceForConsultant, InterfaceForParticipant
{

    public function anInviteeInMeeting(string $firmId, string $meetingId, string $inviteeId): Invitee;

    public function allInviteesInMeeting(string $firmId, string $meetingId, int $page, int $pageSize, ?bool $initiatorStatus);
    
}
