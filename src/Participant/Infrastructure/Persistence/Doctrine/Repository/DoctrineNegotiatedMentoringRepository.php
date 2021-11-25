<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Participant\Domain\Model\Participant\MentoringRequest\NegotiatedMentoring;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequest\NegotiatedMentoringRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineNegotiatedMentoringRepository extends DoctrineEntityRepository implements NegotiatedMentoringRepository
{
    
    public function ofId(string $id): NegotiatedMentoring
    {
        return $this->findOneByIdOrDie($id, 'negotiated mentoring');
    }

}
