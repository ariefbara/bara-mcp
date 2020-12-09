<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\ConsultationSetup;

interface ConsultationSetupRepository
{
    public function aConsultationSetupOfId(string $consultationSetupId): ConsultationSetup;
    public function update(): void;
}
