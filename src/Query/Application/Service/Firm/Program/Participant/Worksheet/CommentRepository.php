<?php

namespace Query\Application\Service\Firm\Program\Participant\Worksheet;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;

interface CommentRepository
{

    public function ofId(WorksheetCompositionId $worksheetCompositionId, string $commentId): Worksheet;

    public function all(WorksheetCompositionId $worksheetCompositionId, int $page, int $pageSize);
}
