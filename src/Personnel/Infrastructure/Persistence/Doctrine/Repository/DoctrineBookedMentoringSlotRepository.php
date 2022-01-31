<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot\BookedMentoringSlot;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringSlot\BookedMentoringSlotRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineBookedMentoringSlotRepository extends DoctrineEntityRepository implements BookedMentoringSlotRepository
{

    public function ofId(string $id): BookedMentoringSlot
    {
        return $this->findOneByIdOrDie($id, 'booked mentoring slot');
    }

}
