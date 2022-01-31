<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest\NegotiatedMentoring;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringRequest\NegotiatedMentoringRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineNegotiatedMentoringRepository extends DoctrineEntityRepository implements NegotiatedMentoringRepository
{

    public function ofId(string $id): NegotiatedMentoring
    {
        return $this->findOneByIdOrDie($id, 'negotiated mentoring');
    }

}
