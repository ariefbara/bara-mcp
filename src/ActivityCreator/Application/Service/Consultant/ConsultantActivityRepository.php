<?php

namespace ActivityCreator\Application\Service\Consultant;

use ActivityCreator\Domain\Model\ConsultantActivity;

interface ConsultantActivityRepository
{

    public function nextIdentity(): string;

    public function add(ConsultantActivity $consultantActivity): void;

    public function aConsultantActivityBelongsToPersonnel(
            string $firmId, string $personnelId, string $consultantActivityId): ConsultantActivity;

    public function update(): void;
}
