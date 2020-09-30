<?php

namespace Query\Domain\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;

interface WorksheetRepository
{

    public function aWorksheetBelongToParticipant(string $participantId, string $worksheetId): Worksheet;

    public function allWorksheetBelongToParticipant(string $participantId, int $page, int $pageSize);

    public function allRootWorksheetsBelongToParticipant(string $participantId, int $page, int $pageSize);

    public function allBranchesOfWorksheetBelongToParticipant(string $participantId, string $worksheetId, int $page,
            int $pageSize);
}
