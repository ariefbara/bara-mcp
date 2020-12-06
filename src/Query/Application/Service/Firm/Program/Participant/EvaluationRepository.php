<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\Evaluation;

interface EvaluationRepository
{

    public function anEvaluationOfInProgram(string $firmId, string $programId, string $evaluationId): Evaluation;

    public function allEvaluationsOfParticipant(
            string $firmId, string $programId, string $participantId, int $page, int $pageSize);
}
