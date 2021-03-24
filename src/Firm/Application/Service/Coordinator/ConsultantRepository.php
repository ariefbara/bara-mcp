<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\Consultant;

interface ConsultantRepository
{

    public function aConsultantOfId(string $consultantId): Consultant;
}
