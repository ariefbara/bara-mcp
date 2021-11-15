<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Consultant;

use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot;

interface MentoringSlotRepository
{

    public function allMentoringSlotsBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, MentoringSlotFilter $filter);

    public function aMentoringSlotBelongsToPersonnel(string $personnelId, string $id): MentoringSlot;

    public function allMentoringSlotInProgram(
            string $programId, int $page, int $pageSize, MentoringSlotFilter $mentoringSlotFilter);

    public function aMentoringSlotInProgram(string $programId, string $id): MentoringSlot;
}
