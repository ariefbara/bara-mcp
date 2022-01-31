<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Participant\Domain\Model\Participant\BookedMentoringSlot;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\BookedMentoringSlotRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineBookedMentoringSlotRepository extends DoctrineEntityRepository implements BookedMentoringSlotRepository
{
    
    public function add(BookedMentoringSlot $bookedMentoringSlot): void
    {
        $this->getEntityManager()->persist($bookedMentoringSlot);
    }

    public function ofId(string $id): BookedMentoringSlot
    {
        return $this->findOneByIdOrDie($id, 'booked mentoring slot');
    }

}
