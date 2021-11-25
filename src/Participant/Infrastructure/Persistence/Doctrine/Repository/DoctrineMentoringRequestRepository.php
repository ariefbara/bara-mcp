<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Participant\Domain\Model\Participant\MentoringRequest;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineMentoringRequestRepository extends DoctrineEntityRepository implements MentoringRequestRepository
{

    public function add(MentoringRequest $mentoringRequest)
    {
        $this->getEntityManager()->persist($mentoringRequest);
    }

    public function ofId(string $id): MentoringRequest
    {
        return $this->findOneByIdOrDie($id, 'mentoring request');
    }

}
