<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Consultant;

use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot;

interface MentoringSlotRepository
{

    public function allMentoringSlotsBelongsToPersonnel(string $personnelId, int $page, int $pageSize, MentoringSlotFilter $filter);

    /**
     * 
     * @param string $personnelId
     * @param string $id
     * @return MentoringSlot
     */
    public function aMentoringSlotBelongsToPersonnel(string $personnelId, string $id);
}
