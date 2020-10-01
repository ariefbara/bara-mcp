<?php

namespace Query\Domain\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;

interface WorksheetRepository
{

    public function ofId(string $firmId, string $programId, string $participantId, string $worksheetId): Worksheet;

    public function all(string $firmId, string $programId, string $participantId, int $page, int $pageSize);

    public function allRootWorksheets(string $firmId, string $programId, string $participantId, int $page, int $pageSize);

    public function allBranchesOfParentWorksheet(
            string $firmId, string $programId, string $participantId, string $worksheetId, int $page, int $pageSize);
}
