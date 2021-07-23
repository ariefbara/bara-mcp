<?php

namespace Personnel\Domain\Task\DedicatedMentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor\EvaluationReport;

interface EvaluationReportRepository
{
    public function ofId(string $id): EvaluationReport;
}
