<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Firm\Application\Service\Coordinator\DedicatedMentorRepository;
use Firm\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineDedicatedMentorRepository extends DoctrineEntityRepository implements DedicatedMentorRepository
{
    
    public function ofId(string $dedicatedMentorId): DedicatedMentor
    {
        return $this->findOneByIdOrDie($dedicatedMentorId, 'dedicated mentor');
    }

}
