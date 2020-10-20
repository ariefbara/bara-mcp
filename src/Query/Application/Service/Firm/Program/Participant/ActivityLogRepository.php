<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Application\Service\ {
    Firm\Client\ProgramParticipation\ActivityLogRepository as InterfaceForClient,
    Firm\Team\ProgramParticipation\ActivityLogRepository as InterfaceForTeam,
    User\ProgramParticipation\ActivityLogRepository as InterfaceOfUser
};

interface ActivityLogRepository extends InterfaceForClient, InterfaceOfUser, InterfaceForTeam
{

    public function allActivityLogsInParticipantOfProgram(
            string $programId, string $participantId, int $page, int $pageSize);
}
