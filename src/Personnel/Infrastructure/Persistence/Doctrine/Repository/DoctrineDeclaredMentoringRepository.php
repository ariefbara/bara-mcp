<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DeclaredMentoring;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\DeclaredMentoringRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineDeclaredMentoringRepository extends DoctrineEntityRepository implements DeclaredMentoringRepository
{

    public function add(DeclaredMentoring $declaredMentoring): void
    {
        $this->getEntityManager()->persist($declaredMentoring);
    }

    public function ofId(string $id): DeclaredMentoring
    {
        return $this->findOneByIdOrDie($id, 'declared mentoring');
    }

}
