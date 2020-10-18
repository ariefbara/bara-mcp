<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Application\Service\ {
    Firm\Client\ProgramParticipation\ActivityLogRepository as InterfaceForClient,
    User\ProgramParticipation\ActivityLogRepository as InterfaceOfUser
};

interface ActivityLogRepository extends InterfaceForClient, InterfaceOfUser
{

    public function allActivityLogsInParticipantOfProgram(
            string $programId, string $participantId, int $page, int $pageSize);
}
