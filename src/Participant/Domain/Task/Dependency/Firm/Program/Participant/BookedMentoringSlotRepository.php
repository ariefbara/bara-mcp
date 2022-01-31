<?php

namespace Participant\Domain\Task\Dependency\Firm\Program\Participant;

use Participant\Domain\Model\Participant\BookedMentoringSlot;

interface BookedMentoringSlotRepository
{

    public function nextIdentity(): string;

    public function add(BookedMentoringSlot $bookedMentoringSlot): void;

    public function ofId(string $id): BookedMentoringSlot;
}
