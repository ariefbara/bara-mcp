<?php

namespace Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot;

interface MentoringSlotRepository
{
    public function nextIdentity(): string;
    
    public function add(MentoringSlot $mentoringSlot): void;
    
    public function ofId(string $id): MentoringSlot;
}
