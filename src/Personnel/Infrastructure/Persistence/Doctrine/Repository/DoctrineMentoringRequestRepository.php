<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringRequestRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineMentoringRequestRepository extends DoctrineEntityRepository implements MentoringRequestRepository
{

    public function ofId(string $id): MentoringRequest
    {
        return $this->findOneByIdOrDie($id, 'mentoring request');
    }

}
