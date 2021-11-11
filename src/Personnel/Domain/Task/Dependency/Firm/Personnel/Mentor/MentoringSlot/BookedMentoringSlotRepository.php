<?php

namespace Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringSlot;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot\BookedMentoringSlot;

interface BookedMentoringSlotRepository
{

    public function ofId(string $id): BookedMentoringSlot;
}
