<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlot;

use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlot;

interface BookedMentoringSlotRepository
{

    public function aBookedMentoringSlotBelongsToPersonnel(string $personnelId, string $id): BookedMentoringSlot;

    public function allBookedMentoringSlotsBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, BookedMentoringSlotFilter $filter);

    public function aBookedMentoringSlotBelongsToParticipant(string $participantId, string $id): BookedMentoringSlot;

    public function aBookedMentoringSlotInProgram(string $programId, string $id): BookedMentoringSlot;
}
