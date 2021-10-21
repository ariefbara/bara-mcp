<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineConsultationSetupRepository extends DoctrineEntityRepository implements ConsultationSetupRepository
{

    public function ofId(string $id): ConsultationSetup
    {
        return $this->findOneByIdOrDie($id, 'consultation setup');
    }

}
