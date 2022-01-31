<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringSlotRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineMentoringSlotRepository extends DoctrineEntityRepository implements MentoringSlotRepository
{

    public function add(MentoringSlot $mentoringSlot): void
    {
        $this->getEntityManager()->persist($mentoringSlot);
    }

    public function ofId(string $id): MentoringSlot
    {
        return $this->findOneByIdOrDie($id, 'mentoring slot');
    }

}
