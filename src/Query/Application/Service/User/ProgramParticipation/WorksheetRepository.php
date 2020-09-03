<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;

interface WorksheetRepository
{

    public function aWorksheetBelongsToUserParticipant(string $userId, string $userParticipantId, string $worksheetId): Worksheet;

    public function allWorksheetBelongsToUserParticipant(
            string $userId, string $userParticipantId, int $page, int $pageSize, ?string $missionId, ?string $parentWorksheetId);
}
