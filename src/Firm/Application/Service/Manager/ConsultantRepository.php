<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\Consultant;

interface ConsultantRepository
{

    public function aConsultantOfId(string $consultantId): Consultant;

    public function update(): void;
}
