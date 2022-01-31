<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Participant\Domain\Model\Participant\DeclaredMentoring;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\DeclaredMentoringRepository;
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
