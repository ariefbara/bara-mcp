<?php

namespace Query\Application\Service;

use Query\Domain\Model\Firm\Program\Consultant;

interface ConsultantRepository
{

    public function anActiveConsultant(string $id): Consultant;

    public function allActiveConsultantInProgram(string $programId, int $page, int $pageSize);
}
