<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant\DedicatedMentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor;

interface DedicatedMentorRepository
{

    public function aDedicatedMentorBelongsToPersonnel(
            string $firmId, string $personnelId, string $dedicatedMentorId): DedicatedMentor;

    public function update(): void;
}
